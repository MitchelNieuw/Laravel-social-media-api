<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'tag', 'email', 'profile_picture', 'jwt_token', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'user_id', 'id');
    }

    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'user_id', 'id');
    }

    public function followed(): HasMany
    {
        return $this->hasMany(Follow::class, 'follow_user_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
