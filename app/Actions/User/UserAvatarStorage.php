<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Http\UploadedFile;

class UserAvatarStorage
{
    public function store(User $user, UploadedFile $file): string
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

    public function deleteForUser(User $user): void
    {
        $avatar = $user->avatar;

        if (! is_string($avatar) || $avatar === '') {
            return;
        }

        if (! str_starts_with($avatar, '/avatars/')) {
            return;
        }

        $path = public_path(ltrim($avatar, '/'));

        if (is_file($path)) {
            unlink($path);
        }
    }
}
