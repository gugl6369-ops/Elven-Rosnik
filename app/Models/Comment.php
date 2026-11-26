<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $entity_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $entity_type
 * @property string $content
 * @property \Illuminate\Support\Carbon $created_at
 * @property bool $is_deleted
 */
class Comment extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'comment';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'entity_id',
        'entity_type',
        'parent_id',
        'content',
        'is_deleted',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'is_deleted' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<Comment, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<Comment, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /** @return MorphTo<Model, $this> */
    public function entity(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }

    /** @return HasMany<Reaction, $this> */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'comment_id');
    }
}