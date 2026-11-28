<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Catalog\Concerns\FormatsPlantListItems;
use App\Models\Plant;
use App\Models\Recipe;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    use FormatsPlantListItems;

    public function __invoke(Request $request): Response
    {
        $favoritePlantIds = $this->favoritePlantIdsForUser($request);
        $favoriteRecipeIds = $this->favoriteRecipeIdsForUser($request);

        $stories = Story::query()
            ->where('is_active', true)
            ->where('active_until', '>', now())
            ->with('author:id,username,avatar,name')
            ->orderByDesc('active_from')
            ->limit(50)
            ->get()
            ->map(fn (Story $story): array => $this->formatStoryListItem($story))
            ->values()
            ->all();

        $plants = Plant::query()
            ->with(['genus:id,genus_name'])
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(8)
            ->through(function (Plant $plant) use ($favoritePlantIds): array {
                return $this->formatPlantListItem($plant, $favoritePlantIds);
            });

        $recipeSections = $plants->count() === 8
            ? [$this->recipeSectionForBoundary(8, $favoriteRecipeIds)]
            : [];

        return Inertia::render('Home', [
            'appName' => config('app.name'),
            'stories' => $stories,
            'plants' => $plants,
            'recipeSections' => $recipeSections,
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        $page = max(1, $request->integer('page', 2));
        $favoritePlantIds = $this->favoritePlantIdsForUser($request);
        $favoriteRecipeIds = $this->favoriteRecipeIdsForUser($request);
        $plants = Plant::query()
            ->with(['genus:id,genus_name'])
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(8, ['*'], 'page', $page)
            ->through(fn (Plant $plant): array => $this->formatPlantListItem($plant, $favoritePlantIds));

        return response()->json([
            'plants' => $plants->items(),
            'recipeSection' => $plants->count() === 8
                ? $this->recipeSectionForBoundary($page * 8, $favoriteRecipeIds)
                : null,
            'hasMore' => $plants->hasMorePages(),
            'nextPage' => $plants->hasMorePages() ? $page + 1 : null,
        ]);
    }

    /**
     * @param  list<int>  $favoriteRecipeIds
     * @return array{after: int, recipes: list<array<string, mixed>>}
     */
    private function recipeSectionForBoundary(int $boundary, array $favoriteRecipeIds): array
    {
        $plantIds = Plant::query()
            ->orderBy('name')
            ->orderBy('id')
            ->skip(max(0, $boundary - 8))
            ->take(16)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        $recipes = Recipe::query()
            ->with('plants:id,name')
            ->orderBy('name')
            ->get();

        $window = array_fill_keys($plantIds, true);
        $ranked = $recipes->map(function (Recipe $recipe) use ($window): array {
            $matchedPlants = $recipe->plants
                ->filter(static fn (Plant $plant): bool => isset($window[$plant->id]))
                ->map(static fn (Plant $plant): array => ['id' => $plant->id, 'name' => $plant->name])
                ->values();

            return [
                'recipe' => $recipe,
                'matches' => $matchedPlants->count(),
                'plants' => $matchedPlants->all(),
            ];
        });

        $matched = $ranked->filter(static fn (array $item): bool => $item['matches'] > 0)
            ->sortByDesc('matches')
            ->values();
        $fallback = $ranked->filter(static fn (array $item): bool => $item['matches'] === 0)
            ->shuffle()
            ->values();

        return [
            'after' => $boundary,
            'recipes' => $matched->concat($fallback)->take(6)->map(static function (array $item) use ($favoriteRecipeIds): array {
                /** @var Recipe $recipe */
                $recipe = $item['recipe'];

                return [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'description' => $recipe->description,
                    'healthy' => $recipe->healthy,
                    'is_favorited' => in_array($recipe->id, $favoriteRecipeIds, true),
                    'plants' => $item['plants'],
                ];
            })->values()->all(),
        ];
    }

    /** @return list<int> */
    private function favoriteRecipeIdsForUser(Request $request): array
    {
        $user = $request->user();

        if ($user === null) {
            return [];
        }

        return $user->favoriteRecipes()
            ->pluck('recipe.id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function formatStoryListItem(Story $story): array
    {
        $author = $story->author;
        $label = $story->caption;

        if ($label === null || trim($label) === '') {
            $label = $author?->name ?? $author?->username ?? 'История';
        }

        return [
            'id' => $story->id,
            'media_url' => $story->media_url,
            'media_type' => $story->media_type,
            'caption' => $story->caption,
            'label' => $label,
            'link_url' => $story->link_url,
            'author' => $author !== null
                ? [
                    'id' => $author->id,
                    'username' => $author->username,
                    'name' => $author->name,
                    'avatar' => $author->avatar,
                ]
                : null,
        ];
    }
}
