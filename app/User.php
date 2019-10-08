<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'tag', 'email', 'profilePicture', 'password',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function followed(): HasMany
    {
        return $this->hasMany(Follow::class, 'follow_user_id', 'id');
    }
}
