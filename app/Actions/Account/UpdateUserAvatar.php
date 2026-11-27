<?php

namespace App\Actions\Account;

use App\Actions\User\UserAvatarStorage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUserAvatar
{
    public function __construct(
        private UserAvatarStorage $avatarStorage,
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(User $user, ?UploadedFile $file): User
    {
        Validator::make(
            ['avatar' => $file],
            [
                'avatar' => [
                    'required',
                    'image',
                    'mimes:jpeg,jpg,png,webp',
                    'max:2048',
                ],
            ],
        )->validate();

        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'avatar' => ['Не удалось загрузить файл.'],
            ]);
        }

        $this->avatarStorage->deleteForUser($user);
        $path = $this->avatarStorage->store($user, $file);
        $user->update(['avatar' => $path]);

        return $user->fresh();
    }
}
