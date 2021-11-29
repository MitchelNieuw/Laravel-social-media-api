<?php

namespace App\Http\Resources;

use App\Repositories\FollowRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'tag' => $this->resource->tag,
            'profilePicture' => $this->resource->profile_picture,
            'createdAt' => $this->resource->created_at,
            'followerCount' => (new FollowRepository())->getFollowersCount($this->resource->id),
            'followingCount' => (new FollowRepository())->getFollowingCount($this->resource->id),
        ];
    }
}
