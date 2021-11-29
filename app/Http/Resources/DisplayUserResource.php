<?php

namespace App\Http\Resources;

use App\Repositories\FollowRepository;
use App\Services\ProfileService;
use Illuminate\Http\Resources\Json\JsonResource;

class DisplayUserResource extends JsonResource
{
    public function __construct(
        public $resource,
        protected int $authenticatedUserId
    ) {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        $arrayStatus = app(ProfileService::class)->getStatusBetweenUsers($this->authenticatedUserId, $this->id);
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'tag' => $this->resource->tag,
            'email' => $this->resource->email,
            'profilePicture' => $this->resource->profile_picture,
            'createdAt' => $this->resource->created_at,
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'followerCount' => (new FollowRepository())->getFollowersCount($this->resource->id),
            'followingCount' => (new FollowRepository())->getFollowingCount($this->resource->id),
            'possibleFollow' => $arrayStatus['possibleToFollow'],
            'possibleUnFollow' => $arrayStatus['possibleToUnFollow'],
            'possibleBan' => $arrayStatus['possibleToBan'],
            'possibleUnBan' => $arrayStatus['possibleToUnBan'],
            'possibleTurnOnNotifications' => $arrayStatus['possibleTurnOnNotifications'],
            'possibleTurnOffNotifications' => $arrayStatus['possibleTurnOffNotifications'],
        ];
    }
}
