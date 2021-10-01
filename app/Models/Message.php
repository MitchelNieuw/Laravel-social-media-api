<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    protected $fillable = [
        'id', 'user_id', 'content', 'image'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'message_id', 'id');
    }

    public function notifications(): MorphTo
    {
        return $this->morphTo();
    }
}
