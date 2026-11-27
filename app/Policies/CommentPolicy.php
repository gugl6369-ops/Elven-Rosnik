<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user): bool
    {
        if ($user->is_blocked ?? false) {
            return false;
        }

        return true;
    }

    public function update(User $user, Comment $comment): bool
    {
        if ($comment->is_deleted) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        return $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        if ($comment->is_deleted) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        return $user->id === $comment->user_id;
    }
}