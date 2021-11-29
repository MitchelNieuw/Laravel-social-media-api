<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    protected $fillable = [
        'id', 'user_id', 'follow_user_id', 'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_user_id', 'id');
    }
}
