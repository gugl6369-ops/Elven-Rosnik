<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

trait SyncsTags
{
    /**
     * @param  list<int>  $tagIds
     * @param  list<array{name: string, category: string}>  $newTags
     */
    protected function syncTags(BelongsToMany $relation, array $tagIds, array $newTags): void
    {
        foreach ($newTags as $newTag) {
            $name = trim($newTag['name']);
            $category = $newTag['category'];
            $normalizedName = Str::lower($name);

            $tag = Tag::query()
                ->where('category', $category)
                ->whereRaw('LOWER(name) = ?', [$normalizedName])
                ->first();

            if ($tag === null) {
                $tag = Tag::query()->create([
                    'name' => $name,
                    'category' => $category,
                ]);
            }

            $tagIds[] = $tag->id;
        }

        $relation->sync(array_values(array_unique(array_map('intval', $tagIds))));
    }
}
