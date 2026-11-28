<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $plant_id
 * @property int $pole_id
 * @property string $x
 * @property string $y
 * @property string $size
 * @property string $rotate
 */
class PlantPole extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'plant_pole';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'plant_id',
        'pole_id',
        'x',
        'y',
        'size',
        'rotate',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'x' => 'decimal:2',
            'y' => 'decimal:2',
            'size' => 'decimal:2',
            'rotate' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Plant, $this> */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    /** @return BelongsTo<Pole, $this> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class, 'pole_id');
    }
}
