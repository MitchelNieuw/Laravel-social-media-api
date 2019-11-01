<?php

namespace App\Http\Resources;

use App\Repositories\FollowRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 */
class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'tag' => $this->tag,
            'profilePicture' => $this->profilePicture,
            'jwtToken' => $this->jwt_token,
            'createdAt' => $this->created_at,
            'followerCount' => (new FollowRepository())->getFollowersCount($this->id),
            'followingCount' => (new FollowRepository())->getFollowingCount($this->id),
        ];
    }
}