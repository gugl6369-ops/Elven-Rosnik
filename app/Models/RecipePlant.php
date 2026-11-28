<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $recipe_id
 * @property int $plant_id
 */
class RecipePlant extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'recipe_plant';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'recipe_id',
        'plant_id',
    ];

    /** @return BelongsTo<Recipe, $this> */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }

    /** @return BelongsTo<Plant, $this> */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
}
