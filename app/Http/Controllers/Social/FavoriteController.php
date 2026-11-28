<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $plants = $user
            ->favoritePlants()
            ->with(['genus:id,genus_name'])
            ->orderBy('name')
            ->paginate(12, ['*'], 'plants_page')
            ->withQueryString()
            ->through(static function (Plant $plant): array {
                return [
                    'id' => $plant->id,
                    'name' => $plant->name,
                    'image_url' => $plant->image_url,
                    'description' => $plant->description,
                    'height' => $plant->height !== null ? (string) $plant->height : null,
                    'watering_frequency' => $plant->watering_frequency,
                    'sunlight' => $plant->sunlight,
                    'temp_min' => $plant->temp_min !== null ? (string) $plant->temp_min : null,
                    'temp_max' => $plant->temp_max !== null ? (string) $plant->temp_max : null,
                    'genus' => $plant->genus !== null
                        ? [
                            'id' => $plant->genus->id,
                            'genus_name' => $plant->genus->genus_name,
                        ]
                        : null,
                    'is_favorited' => true,
                ];
            });

        $recipes = $user
            ->favoriteRecipes()
            ->with('plants:id,name')
            ->orderBy('name')
            ->paginate(12, ['recipe.*'], 'recipes_page')
            ->withQueryString()
            ->through(static function (Recipe $recipe): array {
                return [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'description' => $recipe->description,
                    'healthy' => $recipe->healthy,
                    'is_favorited' => true,
                    'plants' => $recipe->plants->map(static fn (Plant $plant): array => [
                        'id' => $plant->id,
                        'name' => $plant->name,
                    ])->values()->all(),
                ];
            });

        return Inertia::render('account/ui/FavoritesPage', [
            'plants' => $plants,
            'recipes' => $recipes,
        ]);
    }

    public function store(Request $request, Plant $plant): RedirectResponse
    {
        $request->user()->favoritePlants()->syncWithoutDetaching([$plant->id]);

        return back();
    }

    public function destroy(Request $request, Plant $plant): RedirectResponse
    {
        $request->user()->favoritePlants()->detach($plant->id);

        return back();
    }

    public function storeRecipe(Request $request, Recipe $recipe): RedirectResponse
    {
        $request->user()->favoriteRecipes()->syncWithoutDetaching([$recipe->id]);

        return back();
    }

    public function destroyRecipe(Request $request, Recipe $recipe): RedirectResponse
    {
        $request->user()->favoriteRecipes()->detach($recipe->id);

        return back();
    }
}
