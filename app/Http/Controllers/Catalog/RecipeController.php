<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Social\Concerns\FormatsComments;
use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    use FormatsComments;

    public function show(Request $request, Recipe $recipe): Response
    {
        $recipe->load([
            'plants:id,name',
            'tags:id,name,category',
        ]);

        $commentsCount = Comment::query()
            ->where('entity_type', 'recipe')
            ->where('entity_id', $recipe->id)
            ->where('is_deleted', false)
            ->count();

        return Inertia::render('catalog/ui/RecipePage', [
            'recipe' => [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description,
                'additional' => $recipe->additional,
                'advice' => $recipe->advice,
                'recipe' => $recipe->recipe,
                'healthy' => $recipe->healthy,
                'is_favorited' => $request->user()?->favoriteRecipes()
                    ->whereKey($recipe->id)
                    ->exists() ?? false,
                'plants' => $recipe->plants->map(static fn ($plant) => [
                    'id' => $plant->id,
                    'name' => $plant->name,
                ])->values(),
                'tags' => $recipe->tags->map(static fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'category' => $tag->category,
                ])->values(),
                'comments' => $this->loadVisibleCommentsForMorph('recipe', $recipe->id, $request->user()?->id),
                'comments_count' => $commentsCount,
            ],
        ]);
    }
}
