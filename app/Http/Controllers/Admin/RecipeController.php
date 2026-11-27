<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRecipeRequest;
use App\Http\Requests\Admin\UpdateRecipeRequest;
use App\Models\Plant;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $recipes = Recipe::query()
            ->with('plants:id,name')
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%'.$search.'%';
                $query->where(function ($inner) use ($like): void {
                    $inner->where('name', 'ilike', $like)
                        ->orWhere('description', 'ilike', $like);
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString()
            ->through(function (Recipe $recipe): array {
                return [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'description' => $recipe->description,
                    'additional' => $recipe->additional,
                    'advice' => $recipe->advice,
                    'recipe' => $recipe->recipe,
                    'healthy' => $recipe->healthy,
                    'plant_ids' => $recipe->plants->pluck('id')->map(static fn ($id): int => (int) $id)->all(),
                    'plants' => $recipe->plants->map(fn (Plant $plant) => [
                        'id' => $plant->id,
                        'name' => $plant->name,
                    ])->values()->all(),
                ];
            });

        $plantOptions = Plant::query()
            ->orderBy('name')
            ->limit(200)
            ->get(['id', 'name']);

        return Inertia::render('admin/ui/RecipesPage', [
            'recipes' => $recipes,
            'plantOptions' => $plantOptions,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(StoreRecipeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $plantIds = $validated['plant_ids'] ?? [];

        DB::transaction(function () use ($validated, $plantIds): void {
            $recipe = Recipe::query()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'additional' => $validated['additional'] ?? null,
                'advice' => $validated['advice'] ?? null,
                'recipe' => $validated['recipe'],
                'healthy' => (bool) ($validated['healthy'] ?? true),
            ]);

            $recipe->plants()->sync($plantIds);
        });

        return redirect()
            ->route('admin.recipes.index')
            ->with('success', 'Рецепт создан.');
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): RedirectResponse
    {
        $validated = $request->validated();
        $plantIds = $validated['plant_ids'] ?? [];

        DB::transaction(function () use ($recipe, $validated, $plantIds): void {
            $recipe->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'additional' => $validated['additional'] ?? null,
                'advice' => $validated['advice'] ?? null,
                'recipe' => $validated['recipe'],
                'healthy' => (bool) ($validated['healthy'] ?? true),
            ]);

            $recipe->plants()->sync($plantIds);
        });

        return redirect()
            ->route('admin.recipes.index')
            ->with('success', 'Рецепт обновлён.');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $recipe->delete();

        return redirect()
            ->route('admin.recipes.index')
            ->with('success', 'Рецепт удалён.');
    }
}