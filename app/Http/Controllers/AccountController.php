<?php

namespace App\Http\Controllers;

use App\Actions\Account\UpdateUserAvatar;
use App\Http\Requests\Account\UpdateAccountPasswordRequest;
use App\Http\Requests\Account\UpdateAccountProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * @return list<string>
     */
    public static function inertiaUserFields(): array
    {
        return [
            'id',
            'username',
            'name',
            'email',
            'avatar',
            'status',
            'is_admin',
            'pole_id',
            'created_at',
        ];
    }

    public function show(Request $request): Response
    {
        return Inertia::render('account/ui/AccountPage', [
            'user' => $request->user()->only(self::inertiaUserFields()),
        ]);
    }

    public function updateAvatar(
        Request $request,
        UpdateUserAvatar $updateUserAvatar,
    ): RedirectResponse {
        $updateUserAvatar->execute(
            $request->user(),
            $request->file('avatar'),
        );

        return redirect()
            ->route('account')
            ->with('success', 'Аватар обновлён');
    }

    public function updateProfile(UpdateAccountProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->forceFill($request->validated())->save();
        Auth::login($user);
        $request->session()->regenerate();

        return back()->with('success', 'Имя и username обновлены.');
    }

    public function updatePassword(UpdateAccountPasswordRequest $request): RedirectResponse
    {
        $request->user()->forceFill([
            'password' => $request->validated()['password'],
        ])->save();
        $request->session()->regenerate();

        return back()->with('success', 'Пароль изменён.');
    }
}
