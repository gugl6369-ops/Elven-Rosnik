<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Plant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $plants = Plant::query()
            ->with(['genus:id,genus_name'])
            ->orderBy('name')
            ->paginate(12)
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
                ];
            });

        return Inertia::render('catalog/ui/CatalogPage', [
            'plants' => $plants,
        ]);
    }

    public function show(Request $request, Plant $plant): Response
    {
        $plant->load([
            'genus:id,genus_name',
            'tags:id,name',
            'recipes:id,name',
        ]);

        // $plant->loadCount('comments');

        $is_favorited = Favorite::query()
            ->where('user_id', $request->user()->id);

        $couner_favorite = Favorite::query()
            ->where('plant_id', $plant->id)
            ->count();

        $plant = Plant::query()->findOrFail($plant->id);

        return Inertia::render('catalog/ui/PlantPage', [
            'plant' => [
                'id' => $plant->id,
                'name' => $plant->name,
                'image_url' => $plant->image_url,
                'description' => $plant->description,
                'height' => $plant->height,
                'watering_frequency' => $plant->watering_frequency,
                'sunlight' => $plant->sunlight,
                'temp_min' => $plant->temp_min,
                'temp_max' => $plant->temp_max,
                'preparation' => $plant->preparation,
                'storage' => $plant->storage,
                'genus_name' => $plant->genus?->name,
                'tags' => $plant->tags->map(fn ($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'category' => $t->category,
                ])->values(),
                'recipes' => $plant->recipes->map(fn ($r) => [
                    'id' => $r->id,
                    'name' => $r->name,
                ])->values(),
                // 'comments_count' => $plant->comments_count,
            ],
            $is_favorited,
            $couner_favorite,
        ]);

    }
}
