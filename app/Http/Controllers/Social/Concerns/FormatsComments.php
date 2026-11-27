<?php

namespace App\Http\Controllers\Social\Concerns;

use App\Models\Comment;
use Illuminate\Support\Collection;

trait FormatsComments
{
    /**
     * @param  Collection<int, Comment>  $comments
     * @return list<array<string, mixed>>
     */
    protected function formatCommentsForInertia(Collection $comments): array
    {
        return $comments->map(static function (Comment $comment): array {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at?->toIso8601String(),
                'parent_id' => $comment->parent_id,
                'author' => [
                    'id' => $comment->user->id,
                    'username' => $comment->user->username,
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar,
                ],
            ];
        })->values()->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function loadVisibleCommentsForMorph(string $entityType, int $entityId): array
    {
        $comments = Comment::query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('is_deleted', false)
            ->with(['user:id,username,name,avatar'])
            ->orderBy('created_at')
            ->get();

        return $this->formatCommentsForInertia($comments);
    }
}