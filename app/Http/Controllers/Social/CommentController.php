<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Social\Concerns\FormatsComments;
use App\Http\Requests\Social\StoreCommentRequest;
use App\Http\Requests\Social\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Plant;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    use FormatsComments;

    public function storePlant(StoreCommentRequest $request, Plant $plant): RedirectResponse
    {
        return $this->storeForEntity($request, $plant, 'plant');
    }

    public function storeRecipe(StoreCommentRequest $request, Recipe $recipe): RedirectResponse
    {
        return $this->storeForEntity($request, $recipe, 'recipe');
    }

    public function updatePlant(
        UpdateCommentRequest $request,
        Plant $plant,
        Comment $comment,
    ): RedirectResponse {
        return $this->updateForEntity($request, $plant, 'plant', $comment);
    }

    public function updateRecipe(
        UpdateCommentRequest $request,
        Recipe $recipe,
        Comment $comment,
    ): RedirectResponse {
        return $this->updateForEntity($request, $recipe, 'recipe', $comment);
    }

    public function destroyPlant(Plant $plant, Comment $comment): RedirectResponse
    {
        return $this->destroyForEntity($plant, 'plant', $comment);
    }

    public function destroyRecipe(Recipe $recipe, Comment $comment): RedirectResponse
    {
        return $this->destroyForEntity($recipe, 'recipe', $comment);
    }

    private function storeForEntity(
        StoreCommentRequest $request,
        Model $entity,
        string $morphKey,
    ): RedirectResponse {
        $this->authorize('create', Comment::class);

        $validated = $request->validated();
        $parentId = isset($validated['parent_id']) ? (int) $validated['parent_id'] : null;

        $this->assertParentBelongsToEntity($parentId, $morphKey, (int) $entity->getKey());

        /** @var Plant|Recipe $entity */
        $entity->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'parent_id' => $parentId,
            'is_deleted' => false,
        ]);

        return back();
    }

    private function updateForEntity(
        UpdateCommentRequest $request,
        Model $entity,
        string $morphKey,
        Comment $comment,
    ): RedirectResponse {
        $this->assertCommentOnEntity($comment, $morphKey, (int) $entity->getKey());
        $this->authorize('update', $comment);

        $comment->update([
            'content' => $request->validated()['content'],
        ]);

        return back();
    }

    private function destroyForEntity(
        Model $entity,
        string $morphKey,
        Comment $comment,
    ): RedirectResponse {
        $this->assertCommentOnEntity($comment, $morphKey, (int) $entity->getKey());
        $this->authorize('delete', $comment);

        $comment->update(['is_deleted' => true]);

        return back();
    }

    private function assertCommentOnEntity(
        Comment $comment,
        string $morphKey,
        int $entityId,
    ): void {
        if ($comment->entity_type !== $morphKey || $comment->entity_id !== $entityId) {
            abort(404);
        }
    }

    private function assertParentBelongsToEntity(
        ?int $parentId,
        string $morphKey,
        int $entityId,
    ): void {
        if ($parentId === null) {
            return;
        }

        $parent = Comment::query()->find($parentId);

        if ($parent === null) {
            throw ValidationException::withMessages([
                'parent_id' => ['Родительский комментарий не найден.'],
            ]);
        }

        if (
            $parent->is_deleted
            || $parent->entity_type !== $morphKey
            || $parent->entity_id !== $entityId
        ) {
            throw ValidationException::withMessages([
                'parent_id' => ['Ответ возможен только к комментарию этой же карточки.'],
            ]);
        }
    }
}