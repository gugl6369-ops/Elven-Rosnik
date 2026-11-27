<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): Response
    {
        $plants = $request->user()
            ->favoritePlants()
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
                    'is_favorited' => true,
                ];
            });

        return Inertia::render('account/ui/FavoritesPage', [
            'plants' => $plants,
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
}
