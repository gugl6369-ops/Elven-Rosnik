<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresPublicUploads;
use App\Http\Controllers\Admin\Concerns\SyncsTags;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Catalog\Concerns\FormatsPlantListItems;
use App\Http\Requests\Admin\StorePlantRequest;
use App\Http\Requests\Admin\UpdatePlantRequest;
use App\Models\Genus;
use App\Models\Plant;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PlantController extends Controller
{
    use FormatsPlantListItems;
    use StoresPublicUploads;
    use SyncsTags;

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $plants = Plant::query()
            ->with(['genus:id,genus_name', 'tags:id,name'])
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
            ->through(function (Plant $plant): array {
                return [
                    ...$this->formatPlantListItem($plant, []),
                    'tag_ids' => $plant->tags->pluck('id')->map(static fn ($id): int => (int) $id)->all(),
                    'tags' => $plant->tags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ])->values()->all(),
                ];
            });

        return Inertia::render('admin/ui/PlantsPage', [
            'plants' => $plants,
            'genera' => Genus::query()->orderBy('genus_name')->get(['id', 'genus_name']),
            'tags' => Tag::query()->orderBy('category')->orderBy('name')->get(['id', 'name', 'category']),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(StorePlantRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];
        $newTags = $validated['new_tags'] ?? [];

        DB::transaction(function () use ($request, $validated, $tagIds, $newTags): void {
            $genus = $this->resolveGenus($validated);
            $imageUrl = null;
            $file = $request->file('image');

            if ($file !== null) {
                $imageUrl = $this->storePublicImage($file, 'plants');
            }

            $plant = Plant::query()->create([
                'genus_id' => $genus->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'image_url' => $imageUrl,
                'height' => $validated['height'] ?? null,
                'watering_frequency' => $validated['watering_frequency'] ?? null,
                'sunlight' => $validated['sunlight'] ?? null,
                'temp_min' => $validated['temp_min'] ?? null,
                'temp_max' => $validated['temp_max'] ?? null,
            ]);

            $this->syncTags($plant->tags(), $tagIds, $newTags);
        });

        return redirect()
            ->route('admin.plants.index')
            ->with('success', 'Карточка растения создана.');
    }

    public function update(UpdatePlantRequest $request, Plant $plant): RedirectResponse
    {
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];
        $newTags = $validated['new_tags'] ?? [];

        DB::transaction(function () use ($request, $plant, $validated, $tagIds, $newTags): void {
            $genus = $this->resolveGenus($validated);
            $file = $request->file('image');

            if ($file !== null) {
                $this->deletePublicPath($plant->image_url);
                $plant->image_url = $this->storePublicImage($file, 'plants');
            }

            $plant->fill([
                'genus_id' => $genus->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'height' => $validated['height'] ?? null,
                'watering_frequency' => $validated['watering_frequency'] ?? null,
                'sunlight' => $validated['sunlight'] ?? null,
                'temp_min' => $validated['temp_min'] ?? null,
                'temp_max' => $validated['temp_max'] ?? null,
            ]);
            $plant->save();
            $this->syncTags($plant->tags(), $tagIds, $newTags);
        });

        return redirect()
            ->route('admin.plants.index')
            ->with('success', 'Карточка обновлена.');
    }

    public function destroy(Plant $plant): RedirectResponse
    {
        $this->deletePublicPath($plant->image_url);
        $plant->delete();

        return redirect()
            ->route('admin.plants.index')
            ->with('success', 'Карточка удалена.');
    }

    /** @param array<string, mixed> $validated */
    private function resolveGenus(array $validated): Genus
    {
        if (! empty($validated['genus_id'])) {
            return Genus::query()->findOrFail($validated['genus_id']);
        }

        $name = trim((string) $validated['new_genus_name']);

        return Genus::query()
            ->whereRaw('LOWER(genus_name) = ?', [Str::lower($name)])
            ->first()
            ?? Genus::query()->create(['genus_name' => $name]);
    }
}
