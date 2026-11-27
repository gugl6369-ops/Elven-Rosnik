<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demo stories for the home feed strip (local dev).
 *
 * Run: php artisan db:seed --class=StoryDemoSeeder
 * Requires at least one user (e.g. after registration or elfi:ensure-admin).
 */
class StoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()
            ->where('is_admin', true)
            ->orderBy('id')
            ->first()
            ?? User::query()->orderBy('id')->first();

        if ($author === null) {
            $this->command?->warn('StoryDemoSeeder: no users in DB — skip. Create a user first.');

            return;
        }

        $now = Carbon::now();
        $definitions = [
            [
                'caption' => 'Весенний сад',
                'media_url' => 'https://picsum.photos/seed/story-spring/400/400',
                'link_url' => '/catalog',
            ],
            [
                'caption' => 'Сбор мяты',
                'media_url' => 'https://picsum.photos/seed/story-mint/400/400',
                'link_url' => null,
            ],
            [
                'caption' => 'Новинки каталога',
                'media_url' => 'https://picsum.photos/seed/story-catalog/400/400',
                'link_url' => '/catalog',
            ],
            [
                'caption' => 'Лаванда в цвету',
                'media_url' => 'https://picsum.photos/seed/story-lavender/400/400',
                'link_url' => null,
            ],
            [
                'caption' => 'Совет недели',
                'media_url' => 'https://picsum.photos/seed/story-tip/400/400',
                'link_url' => null,
            ],
            [
                'caption' => 'Рецепты с травами',
                'media_url' => 'https://picsum.photos/seed/story-recipes/400/400',
                'link_url' => '/catalog',
            ],
        ];

        foreach ($definitions as $definition) {
            Story::query()->firstOrCreate(
                [
                    'author_id' => $author->id,
                    'caption' => $definition['caption'],
                ],
                [
                    'media_url' => $definition['media_url'],
                    'media_type' => 'image',
                    'link_url' => $definition['link_url'],
                    'active_from' => $now->copy()->subHours(2),
                    'active_until' => $now->copy()->addDays(2),
                    'is_active' => true,
                ],
            );
        }
    }
}
