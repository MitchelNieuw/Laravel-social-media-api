<?php

namespace App\Http\Resources;

use App\Repositories\BanRepository;
use App\Repositories\FollowRepository;
use App\Repositories\NotificationRepository;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisplayUserResource extends JsonResource
{
    /**
     * @var int
     */
    protected $authenticatedUserId;

    /**
     * @param $resource
     * @param int $authenticatedUserId
     */
    public function __construct($resource, int $authenticatedUserId)
    {
        parent::__construct($resource);
        $this->authenticatedUserId = $authenticatedUserId;
    }

    /**
     * @param Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $arrayStatus = app(ProfileService::class)->getStatusBetweenUsers($this->authenticatedUserId, $this->id);
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'tag' => $this->resource->tag,
            'email' => $this->resource->email,
            'profilePicture' => $this->resource->profilePicture,
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
