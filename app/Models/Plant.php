<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $genus_id
 * @property string $name
 * @property string|null $image_url
 * @property string|null $height
 * @property string|null $description
 * @property string|null $watering_frequency
 * @property string|null $temp_max
 * @property string|null $temp_min
 * @property string|null $sunlight
 * @property string|null $collection
 * @property string|null $preparation
 * @property string|null $storage
 * @property string|null $readiness_check
 * @property string|null $drying
 */
class Plant extends Model
{
    public $timestamps = false;

    protected $table = 'plant';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'genus_id',
        'name',
        'image_url',
        'height',
        'description',
        'watering_frequency',
        'temp_max',
        'temp_min',
        'sunlight',
        'collection',
        'preparation',
        'storage',
        'readiness_check',
        'drying',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'height' => 'decimal:2',
            'temp_max' => 'decimal:2',
            'temp_min' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Genus, $this> */
    public function genus(): BelongsTo
    {
        return $this->belongsTo(Genus::class, 'genus_id');
    }

    /** @return HasMany<PlantPole, $this> */
    public function plantPoles(): HasMany
    {
        return $this->hasMany(PlantPole::class, 'plant_id');
    }

    /** @return BelongsToMany<Pole, $this, Pivot, 'pivot'> */
    public function poles(): BelongsToMany
    {
        return $this->belongsToMany(Pole::class, 'plant_pole')
            ->withPivot(['x', 'y', 'size', 'rotate']);
    }

    /** @return HasMany<PlantTag, $this> */
    public function plantTags(): HasMany
    {
        return $this->hasMany(PlantTag::class, 'plant_id');
    }

    /** @return BelongsToMany<Tag, $this, Pivot, 'pivot'> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'plant_tag', 'plant_id', 'tag_id');
    }

    /** @return HasMany<Favorite, $this> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'plant_id');
    }

    /** @return BelongsToMany<User, $this, Pivot, 'pivot'> */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite', 'plant_id', 'user_id');
    }

    /** @return HasMany<RecipePlant, $this> */
    public function recipePlants(): HasMany
    {
        return $this->hasMany(RecipePlant::class, 'plant_id');
    }

    /** @return BelongsToMany<Recipe, $this, Pivot, 'pivot'> */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_plant', 'plant_id', 'recipe_id');
    }

    /** @return MorphMany<Comment, $this> */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'entity', 'entity_type', 'entity_id');
    }
}