<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string|null $background
 */
class Pole extends Model
{
    public $timestamps = false;

    protected $table = 'pole';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'background',
    ];

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'pole_id');
    }

    /** @return HasMany<PlantPole, $this> */
    public function plantPoles(): HasMany
    {
        return $this->hasMany(PlantPole::class, 'pole_id');
    }

    /** @return BelongsToMany<Plant, $this, Pivot, 'pivot'> */
    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class, 'plant_pole')
            ->withPivot(['x', 'y', 'size', 'rotate']);
    }
}