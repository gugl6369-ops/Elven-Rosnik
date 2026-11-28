<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $plant_id
 * @property int $tag_id
 */
class PlantTag extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'plant_tag';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'plant_id',
        'tag_id',
    ];

    /** @return BelongsTo<Plant, $this> */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    /** @return BelongsTo<Tag, $this> */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
