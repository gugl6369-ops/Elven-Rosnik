<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property int $comment_id
 * @property string $reaction_type
 */
class Reaction extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'reaction';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'comment_id',
        'reaction_type',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<Comment, $this> */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
