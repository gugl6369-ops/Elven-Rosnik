<?php

namespace App\Http\Controllers\Catalog\Concerns;

use App\Models\Plant;
use Illuminate\Http\Request;

trait FormatsPlantListItems
{
    /**
     * @return list<int>
     */
    protected function favoritePlantIdsForUser(Request $request): array
    {
        $user = $request->user();

        if ($user === null) {
            return [];
        }

        return $user->favoritePlants()
            ->pluck('plant.id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param  list<int>  $favoritePlantIds
     * @return array<string, mixed>
     */
    protected function formatPlantListItem(Plant $plant, array $favoritePlantIds): array
    {
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
            'is_favorited' => in_array($plant->id, $favoritePlantIds, true),
        ];
    }
}
