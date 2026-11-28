<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $name
 * @property string $category
 */
class Tag extends Model
{
    public const CATEGORIES = [
        'эффекты',
        'противопоказания',
        'возможные применения',
    ];

    public $timestamps = false;

    protected $table = 'tag';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
    ];

    /** @return HasMany<PlantTag, $this> */
    public function plantTags(): HasMany
    {
        return $this->hasMany(PlantTag::class, 'tag_id');
    }

    /** @return BelongsToMany<Plant, $this, Pivot, 'pivot'> */
    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class, 'plant_tag', 'tag_id', 'plant_id');
    }

    /** @return BelongsToMany<Recipe, $this, Pivot, 'pivot'> */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_tag', 'tag_id', 'recipe_id');
    }
}
