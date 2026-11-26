<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int|null $pole_id
 * @property bool $is_admin
 * @property string $username
 * @property string|null $avatar
 * @property string|null $email
 * @property string $password
 * @property string|null $name
 * @property string|null $status
 * @property string|null $tiktok
 * @property string|null $vk
 * @property string|null $telegram
 * @property bool $is_blocked
 * @property \Illuminate\Support\Carbon $created_at
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'id';

    public const UPDATED_AT = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'pole_id',
        'username',
        'name',
        'email',
        'password',
        'avatar',
        'status',
        'tiktok',
        'vk',
        'telegram',
        'is_admin',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'is_blocked' => 'boolean',
            'created_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** @return BelongsTo<Pole, $this> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class, 'pole_id');
    }

    /** @return HasMany<Comment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /** @return HasMany<Reaction, $this> */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'user_id');
    }

    /** @return HasMany<Favorite, $this> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    /** @return BelongsToMany<Plant, $this, Pivot, 'pivot'> */
    public function favoritePlants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class, 'favorite', 'user_id', 'plant_id');
    }

    public function getAuthIdentifierName(): string
    {
        return 'username';
    }
}
