<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $genus_name
 */
class Genus extends Model
{
    public $timestamps = false;

    protected $table = 'genus';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'genus_name',
    ];

    /** @return HasMany<Plant, $this> */
    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class, 'genus_id');
    }
}