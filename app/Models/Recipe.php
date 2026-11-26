<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $name
 * @property string|null $additional
 * @property string|null $description
 * @property string|null $advice
 * @property string $recipe
 * @property bool $healthy
 */
class Recipe extends Model
{
    public $timestamps = false;

    protected $table = 'recipe';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'additional',
        'description',
        'advice',
        'recipe',
        'healthy',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'healthy' => 'boolean',
        ];
    }

    /** @return HasMany<RecipePlant, $this> */
    public function recipePlants(): HasMany
    {
        return $this->hasMany(RecipePlant::class, 'recipe_id');
    }

    /** @return BelongsToMany<Plant, $this, Pivot, 'pivot'> */
    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class, 'recipe_plant', 'recipe_id', 'plant_id');
    }

    /** @return MorphMany<Comment, $this> */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'entity', 'entity_type', 'entity_id');
    }
}