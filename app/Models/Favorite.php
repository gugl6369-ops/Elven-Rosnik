<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Junction: composite PK (user_id, plant_id).
 *
 * @property int $user_id
 * @property int $plant_id
 */
class Favorite extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'favorite';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'plant_id',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<Plant, $this> */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
}