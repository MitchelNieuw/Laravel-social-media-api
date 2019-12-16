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
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'tag' => $this->resource->tag,
            'profilePicture' => $this->resource->profilePicture,
            'createdAt' => $this->resource->created_at,
            'followerCount' => (new FollowRepository())->getFollowersCount($this->resource->id),
            'followingCount' => (new FollowRepository())->getFollowingCount($this->resource->id),
        ];
    }
}
