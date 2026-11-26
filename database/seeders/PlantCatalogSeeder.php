<?php

namespace Database\Seeders;

use App\Models\Genus;
use App\Models\Plant;
use Illuminate\Database\Seeder;

/**
 * Optional demo rows for the plant catalog. Not called from DatabaseSeeder.
 *
 * Run: php artisan db:seed --class=PlantCatalogSeeder
 */
class PlantCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $rosa = Genus::query()->firstOrCreate(
            ['genus_name' => 'Rosa'],
        );

        $mentha = Genus::query()->firstOrCreate(
            ['genus_name' => 'Mentha'],
        );

        Plant::query()->firstOrCreate(
            [
                'genus_id' => $rosa->id,
                'name' => 'Шиповник дикий',
            ],
            [
                'description' => 'Декоративный куст с плодами, богатых витамином C.',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '1.50',
                'temp_min' => '5.00',
                'temp_max' => '30.00',
            ],
        );

        Plant::query()->firstOrCreate(
            [
                'genus_id' => $mentha->id,
                'name' => 'Мята перечная',
            ],
            [
                'description' => 'Ароматная трава для чая и кулинарии.',
                'watering_frequency' => 'часто',
                'sunlight' => 'полутень',
                'height' => '0.60',
                'temp_min' => '10.00',
                'temp_max' => '28.00',
            ],
        );
    }
}