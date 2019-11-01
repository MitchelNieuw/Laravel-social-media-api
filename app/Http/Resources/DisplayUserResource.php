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
            'id' => $this->id,
            'name' => $this->name,
            'tag' => $this->tag,
            'email' => $this->email,
            'profilePicture' => $this->profilePicture,
            'createdAt' => $this->created_at,
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'followerCount' => (new FollowRepository())->getFollowersCount($this->id),
            'followingCount' => (new FollowRepository())->getFollowingCount($this->id),
            'possibleFollow' => $arrayStatus['possibleToFollow'],
            'possibleUnFollow' => $arrayStatus['possibleToUnFollow'],
            'possibleBan' => $arrayStatus['possibleToBan'],
            'possibleUnBan' => $arrayStatus['possibleToUnBan'],
            'possibleTurnOnNotifications' => $arrayStatus['possibleTurnOnNotifications'],
            'possibleTurnOffNotifications' => $arrayStatus['possibleTurnOffNotifications'],
        ];
    }
}
