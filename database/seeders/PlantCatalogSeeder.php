<?php

namespace Database\Seeders;

use App\Models\Genus;
use App\Models\Plant;
use App\Models\PlantTag;
use App\Models\Tag;
use Illuminate\Database\Seeder;

/**
 * Demo catalog for local development. Idempotent via firstOrCreate on genus, plant name, and tags.
 *
 * Run: php artisan db:seed --class=PlantCatalogSeeder
 */
class PlantCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $genera = $this->ensureGenera();
        $tags = $this->ensureTags();

        foreach ($this->plantDefinitions() as $definition) {
            $genus = $genera[$definition['genus']];

            $plant = Plant::query()->firstOrCreate(
                [
                    'genus_id' => $genus->id,
                    'name' => $definition['name'],
                ],
                [
                    'description' => $definition['description'],
                    'image_url' => $definition['image_url'],
                    'watering_frequency' => $definition['watering_frequency'],
                    'sunlight' => $definition['sunlight'],
                    'height' => $definition['height'],
                    'temp_min' => $definition['temp_min'],
                    'temp_max' => $definition['temp_max'],
                ],
            );

            foreach ($definition['tag_keys'] as $tagKey) {
                $tag = $tags[$tagKey];
                PlantTag::query()->firstOrCreate([
                    'plant_id' => $plant->id,
                    'tag_id' => $tag->id,
                ]);
            }
        }
    }

    /**
     * @return array<string, Genus>
     */
    private function ensureGenera(): array
    {
        $names = [
            'Rosa',
            'Mentha',
            'Lavandula',
            'Ocimum',
            'Salvia',
            'Thymus',
            'Petroselinum',
            'Solanum',
            'Capsicum',
            'Cucumis',
            'Allium',
            'Beta',
            'Daucus',
            'Fragaria',
            'Vaccinium',
            'Rubus',
            'Helianthus',
            'Tagetes',
            'Pelargonium',
        ];

        $genera = [];
        foreach ($names as $name) {
            $genera[$name] = Genus::query()->firstOrCreate(['genus_name' => $name]);
        }

        return $genera;
    }

    /**
     * @return array<string, Tag>
     */
    private function ensureTags(): array
    {
        $definitions = [
            'tea' => ['name' => 'чай', 'category' => 'использование'],
            'kitchen' => ['name' => 'кухня', 'category' => 'использование'],
            'decorative' => ['name' => 'декоративное', 'category' => 'тип'],
            'vegetable' => ['name' => 'овощ', 'category' => 'тип'],
            'berry' => ['name' => 'ягода', 'category' => 'тип'],
            'herb' => ['name' => 'пряность', 'category' => 'тип'],
            'easy' => ['name' => 'неприхотливое', 'category' => 'уход'],
            'indoor' => ['name' => 'комнатное', 'category' => 'место'],
        ];

        $tags = [];
        foreach ($definitions as $key => $attrs) {
            $tags[$key] = Tag::query()->firstOrCreate($attrs);
        }

        return $tags;
    }

    /**
     * @return list<array{
     *     genus: string,
     *     name: string,
     *     description: string,
     *     image_url: string,
     *     watering_frequency: string,
     *     sunlight: string,
     *     height: string,
     *     temp_min: string,
     *     temp_max: string,
     *     tag_keys: list<string>
     * }>
     */
    private function plantDefinitions(): array
    {
        return [
            [
                'genus' => 'Rosa',
                'name' => 'Шиповник дикий',
                'description' => 'Декоративный куст с плодами, богатых витамином C. Подходит для живой изгороди и чайных настоев.',
                'image_url' => 'https://picsum.photos/seed/wild-rose/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '1.50',
                'temp_min' => '5.00',
                'temp_max' => '30.00',
                'tag_keys' => ['decorative', 'tea', 'easy'],
            ],
            [
                'genus' => 'Rosa',
                'name' => 'Роза чайная',
                'description' => 'Классическая садовая роза с насыщенным ароматом и повторным цветением.',
                'image_url' => 'https://picsum.photos/seed/tea-rose/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '1.20',
                'temp_min' => '8.00',
                'temp_max' => '32.00',
                'tag_keys' => ['decorative'],
            ],
            [
                'genus' => 'Mentha',
                'name' => 'Мята перечная',
                'description' => 'Ароматная трава для чая, лимонада и свежих салатов.',
                'image_url' => 'https://picsum.photos/seed/peppermint/640/480',
                'watering_frequency' => 'часто',
                'sunlight' => 'полутень',
                'height' => '0.60',
                'temp_min' => '10.00',
                'temp_max' => '28.00',
                'tag_keys' => ['tea', 'kitchen', 'herb', 'easy'],
            ],
            [
                'genus' => 'Lavandula',
                'name' => 'Лаванда узколистная',
                'description' => 'Серебристые побеги и сиреневые соцветия; медонос и ароматерапия.',
                'image_url' => 'https://picsum.photos/seed/lavender/640/480',
                'watering_frequency' => 'редко',
                'sunlight' => 'полное солнце',
                'height' => '0.80',
                'temp_min' => '5.00',
                'temp_max' => '35.00',
                'tag_keys' => ['decorative', 'tea', 'easy'],
            ],
            [
                'genus' => 'Ocimum',
                'name' => 'Базилик фиолетовый',
                'description' => 'Пряная зелень с лёгким гвоздичным ароматом; хорош на подоконнике.',
                'image_url' => 'https://picsum.photos/seed/purple-basil/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '0.45',
                'temp_min' => '15.00',
                'temp_max' => '30.00',
                'tag_keys' => ['kitchen', 'herb', 'indoor'],
            ],
            [
                'genus' => 'Salvia',
                'name' => 'Шалфей мускатный',
                'description' => 'Душистый шалфей для соусов и чая; выдерживает засуху.',
                'image_url' => 'https://picsum.photos/seed/sage/640/480',
                'watering_frequency' => 'редко',
                'sunlight' => 'полное солнце',
                'height' => '0.70',
                'temp_min' => '8.00',
                'temp_max' => '32.00',
                'tag_keys' => ['kitchen', 'herb', 'tea', 'easy'],
            ],
            [
                'genus' => 'Thymus',
                'name' => 'Тимьян обыкновенный',
                'description' => 'Низкий почвопокровник с эфирными маслами; идеален для альпийских горок.',
                'image_url' => 'https://picsum.photos/seed/thyme/640/480',
                'watering_frequency' => 'редко',
                'sunlight' => 'полное солнце',
                'height' => '0.25',
                'temp_min' => '5.00',
                'temp_max' => '30.00',
                'tag_keys' => ['kitchen', 'herb', 'decorative', 'easy'],
            ],
            [
                'genus' => 'Petroselinum',
                'name' => 'Петрушка кудрявая',
                'description' => 'Универсальная зелень для супов и гарниров; растёт в полутени.',
                'image_url' => 'https://picsum.photos/seed/parsley/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полутень',
                'height' => '0.35',
                'temp_min' => '10.00',
                'temp_max' => '25.00',
                'tag_keys' => ['kitchen', 'herb', 'easy'],
            ],
            [
                'genus' => 'Solanum',
                'name' => 'Томат черри',
                'description' => 'Компактные кусты с сладкими плодами для балкона и теплицы.',
                'image_url' => 'https://picsum.photos/seed/cherry-tomato/640/480',
                'watering_frequency' => 'часто',
                'sunlight' => 'полное солнце',
                'height' => '1.00',
                'temp_min' => '12.00',
                'temp_max' => '32.00',
                'tag_keys' => ['vegetable', 'kitchen'],
            ],
            [
                'genus' => 'Capsicum',
                'name' => 'Перец чили',
                'description' => 'Острый перец длинного плода; требует тёплого места и регулярного полива.',
                'image_url' => 'https://picsum.photos/seed/chili/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '0.90',
                'temp_min' => '18.00',
                'temp_max' => '35.00',
                'tag_keys' => ['vegetable', 'kitchen', 'herb'],
            ],
            [
                'genus' => 'Cucumis',
                'name' => 'Огурец тепличный',
                'description' => 'Самоопыляющийся сорт для закрытого грунта и вертикальной шпалеры.',
                'image_url' => 'https://picsum.photos/seed/cucumber/640/480',
                'watering_frequency' => 'часто',
                'sunlight' => 'полное солнце',
                'height' => '2.00',
                'temp_min' => '15.00',
                'temp_max' => '30.00',
                'tag_keys' => ['vegetable', 'kitchen'],
            ],
            [
                'genus' => 'Allium',
                'name' => 'Лук репчатый',
                'description' => 'Основа кухонного набора; выращивают из севка или семян.',
                'image_url' => 'https://picsum.photos/seed/onion/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '0.40',
                'temp_min' => '5.00',
                'temp_max' => '28.00',
                'tag_keys' => ['vegetable', 'kitchen', 'easy'],
            ],
            [
                'genus' => 'Beta',
                'name' => 'Свёкла столовая',
                'description' => 'Корнеплод для варки и соков; переносит лёгкие заморозки.',
                'image_url' => 'https://picsum.photos/seed/beetroot/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '0.50',
                'temp_min' => '8.00',
                'temp_max' => '26.00',
                'tag_keys' => ['vegetable', 'kitchen', 'easy'],
            ],
            [
                'genus' => 'Daucus',
                'name' => 'Морковь Нантская',
                'description' => 'Сладкий корнеплод на рыхлой почве; дружное всхождение при равномерной влаге.',
                'image_url' => 'https://picsum.photos/seed/carrot/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '0.35',
                'temp_min' => '7.00',
                'temp_max' => '25.00',
                'tag_keys' => ['vegetable', 'kitchen'],
            ],
            [
                'genus' => 'Fragaria',
                'name' => 'Земляника садовая',
                'description' => 'Клубника для грядок и кашпо; даёт урожай при освещении от 6 часов.',
                'image_url' => 'https://picsum.photos/seed/strawberry/640/480',
                'watering_frequency' => 'часто',
                'sunlight' => 'полное солнце',
                'height' => '0.25',
                'temp_min' => '5.00',
                'temp_max' => '28.00',
                'tag_keys' => ['berry', 'kitchen'],
            ],
            [
                'genus' => 'Vaccinium',
                'name' => 'Черника высокая',
                'description' => 'Кислолюбивая ягода для кустарниковых посадок и десертов.',
                'image_url' => 'https://picsum.photos/seed/blueberry/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '1.80',
                'temp_min' => '0.00',
                'temp_max' => '30.00',
                'tag_keys' => ['berry', 'kitchen', 'decorative'],
            ],
            [
                'genus' => 'Rubus',
                'name' => 'Малина ремонтантная',
                'description' => 'Даёт плоды до осени; нуждается в подвязке и обрезке.',
                'image_url' => 'https://picsum.photos/seed/raspberry/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полутень',
                'height' => '2.00',
                'temp_min' => '5.00',
                'temp_max' => '30.00',
                'tag_keys' => ['berry', 'kitchen'],
            ],
            [
                'genus' => 'Helianthus',
                'name' => 'Подсолнечник декоративный',
                'description' => 'Высокий однолетник с крупными жёлтыми корзинками и семенами для птиц.',
                'image_url' => 'https://picsum.photos/seed/sunflower/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '2.50',
                'temp_min' => '10.00',
                'temp_max' => '35.00',
                'tag_keys' => ['decorative', 'easy'],
            ],
            [
                'genus' => 'Tagetes',
                'name' => 'Бархатцы отклонённые',
                'description' => 'Яркие однолетники, отпугивающие вредителей у овощных грядок.',
                'image_url' => 'https://picsum.photos/seed/marigold/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '0.50',
                'temp_min' => '12.00',
                'temp_max' => '32.00',
                'tag_keys' => ['decorative', 'easy'],
            ],
            [
                'genus' => 'Pelargonium',
                'name' => 'Герань комнатная',
                'description' => 'Популярное комнатное растение с ароматной листвой и длительным цветением.',
                'image_url' => 'https://picsum.photos/seed/geranium/640/480',
                'watering_frequency' => 'умеренно',
                'sunlight' => 'полное солнце',
                'height' => '0.60',
                'temp_min' => '12.00',
                'temp_max' => '28.00',
                'tag_keys' => ['decorative', 'indoor', 'easy'],
            ],
        ];
    }
}