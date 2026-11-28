<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Catalog\Concerns\FormatsPlantListItems;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Social\Concerns\FormatsComments;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Plant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    use FormatsComments;
    use FormatsPlantListItems;

    public function index(Request $request): Response
    {
        $favoritePlantIds = $this->favoritePlantIdsForUser($request);
        $search = $request->string('search')->trim()->toString();

        $plants = Plant::query()
            ->with(['genus:id,genus_name'])
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%'.$search.'%';
                $query->where(function ($inner) use ($like): void {
                    $inner->where('name', 'ilike', $like)
                        ->orWhere('description', 'ilike', $like);
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString()
            ->through(function (Plant $plant) use ($favoritePlantIds): array {
                return $this->formatPlantListItem($plant, $favoritePlantIds);
            });

        return Inertia::render('catalog/ui/CatalogPage', [
            'plants' => $plants,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function show(Request $request, Plant $plant): Response
    {
        $plant->load([
            'genus:id,genus_name',
            'tags:id,name,category',
            'recipes:id,name',
        ]);

        $user = $request->user();
        $isFavorited = false;

        if ($user !== null) {
            $isFavorited = Favorite::query()
                ->where('user_id', $user->id)
                ->where('plant_id', $plant->id)
                ->exists();
        }

        $favoritesCount = Favorite::query()
            ->where('plant_id', $plant->id)
            ->count();

        $commentsCount = Comment::query()
            ->where('entity_type', 'plant')
            ->where('entity_id', $plant->id)
            ->where('is_deleted', false)
            ->count();

        return Inertia::render('catalog/ui/PlantPage', [
            'plant' => [
                'id' => $plant->id,
                'name' => $plant->name,
                'image_url' => $plant->image_url,
                'description' => $plant->description,
                'height' => $plant->height !== null ? (string) $plant->height : null,
                'watering_frequency' => $plant->watering_frequency,
                'sunlight' => $plant->sunlight,
                'temp_min' => $plant->temp_min !== null ? (string) $plant->temp_min : null,
                'temp_max' => $plant->temp_max !== null ? (string) $plant->temp_max : null,
                'preparation' => $plant->preparation,
                'storage' => $plant->storage,
                'collection' => $plant->collection,
                'readiness_check' => $plant->readiness_check,
                'drying' => $plant->drying,
                'genus' => $plant->genus !== null
                    ? [
                        'id' => $plant->genus->id,
                        'genus_name' => $plant->genus->genus_name,
                    ]
                    : null,
                'tags' => $plant->tags->map(fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'category' => $tag->category ?? '',
                ])->values(),
                'recipes' => $plant->recipes->map(fn ($recipe) => [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                ])->values(),
                'is_favorited' => $isFavorited,
                'favorites_count' => $favoritesCount,
                'comments' => $this->loadVisibleCommentsForMorph('plant', $plant->id, $user?->id),
                'comments_count' => $commentsCount,
            ],
        ]);
    }
}
