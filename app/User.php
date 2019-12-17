<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @package App
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'tag', 'email', 'profilePicture', 'jwt_token', 'password',
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
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'user_id', 'id');
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


    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
