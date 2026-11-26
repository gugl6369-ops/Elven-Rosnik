<?php

namespace App\Actions\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompleteUserRegistration
{
    use PasswordValidationRules;

    /**
     * @param  array<string, mixed>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        $input['username'] = Str::lower((string) ($input['username'] ?? ''));

        $validated = Validator::make($input, [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'password' => $this->passwordRules(),
            'username' => [
                'required',
                'string',
                'max:64',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique(User::class, 'username'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:32'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ])->validate();

        return DB::transaction(function () use ($validated, $input): User {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'name' => $validated['name'],
                'status' => $validated['status'],
                'is_admin' => false,
            ]);

            $avatar = $input['avatar'] ?? null;

            if ($avatar instanceof UploadedFile && $avatar->isValid()) {
                $path = $this->storeAvatar($user, $avatar);
                $user->update(['avatar' => $path]);
            }

            return $user->fresh();
        });
    }

    private function storeAvatar(User $user, UploadedFile $file): string
    {
        $directory = public_path('avatars');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $user->id.'.'.$extension;
        $file->move($directory, $filename);

        return '/avatars/'.$filename;
    }
}