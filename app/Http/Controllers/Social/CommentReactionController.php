<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Plant;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CommentReactionController extends Controller
{
    private const REACTION_TYPES = ['like', 'heart', 'plant'];

    public function storePlant(Request $request, Plant $plant, Comment $comment): RedirectResponse
    {
        return $this->store($request, $plant, 'plant', $comment);
    }

    public function storeRecipe(Request $request, Recipe $recipe, Comment $comment): RedirectResponse
    {
        return $this->store($request, $recipe, 'recipe', $comment);
    }

    public function destroyPlant(Request $request, Plant $plant, Comment $comment): RedirectResponse
    {
        return $this->destroy($request, $plant, 'plant', $comment);
    }

    public function destroyRecipe(Request $request, Recipe $recipe, Comment $comment): RedirectResponse
    {
        return $this->destroy($request, $recipe, 'recipe', $comment);
    }

    private function store(Request $request, Model $entity, string $entityType, Comment $comment): RedirectResponse
    {
        $this->authorize('create', Comment::class);
        $this->assertCommentOnEntity($comment, $entity, $entityType);

        $validated = $request->validate([
            'reaction_type' => ['required', 'string', Rule::in(self::REACTION_TYPES)],
        ]);

        DB::table('reaction')->upsert(
            [[
                'user_id' => $request->user()->id,
                'comment_id' => $comment->id,
                'reaction_type' => $validated['reaction_type'],
            ]],
            ['user_id', 'comment_id'],
            ['reaction_type'],
        );

        return back();
    }

    private function destroy(Request $request, Model $entity, string $entityType, Comment $comment): RedirectResponse
    {
        $this->authorize('create', Comment::class);
        $this->assertCommentOnEntity($comment, $entity, $entityType);

        DB::table('reaction')
            ->where('user_id', $request->user()->id)
            ->where('comment_id', $comment->id)
            ->delete();

        return back();
    }

    private function assertCommentOnEntity(Comment $comment, Model $entity, string $entityType): void
    {
        if (
            $comment->is_deleted
            || $comment->entity_type !== $entityType
            || (int) $comment->entity_id !== (int) $entity->getKey()
        ) {
            abort(404);
        }
    }
}
