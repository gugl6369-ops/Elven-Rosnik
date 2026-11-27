<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $author_id
 * @property string $media_url
 * @property string $media_type
 * @property string|null $caption
 * @property string|null $link_url
 * @property Carbon $active_from
 * @property Carbon $active_until
 * @property bool $is_active
 * @property Carbon $created_at
 */
class Story extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'story';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'author_id',
        'media_url',
        'media_type',
        'caption',
        'link_url',
        'active_from',
        'active_until',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active_from' => 'datetime',
            'active_until' => 'datetime',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
