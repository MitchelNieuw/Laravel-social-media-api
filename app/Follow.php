<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package App
 */
class Follow extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $table = 'follow';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_user_id', 'id');
    }
}
