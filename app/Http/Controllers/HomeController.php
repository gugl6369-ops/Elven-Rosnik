<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Catalog\Concerns\FormatsPlantListItems;
use App\Models\Plant;
use App\Models\Story;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    use FormatsPlantListItems;

    public function __invoke(Request $request): Response
    {
        $favoritePlantIds = $this->favoritePlantIdsForUser($request);

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
            ->paginate(12)
            ->through(function (Plant $plant) use ($favoritePlantIds): array {
                return $this->formatPlantListItem($plant, $favoritePlantIds);
            });

        return Inertia::render('Home', [
            'appName' => config('app.name'),
            'stories' => $stories,
            'plants' => $plants,
        ]);
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
