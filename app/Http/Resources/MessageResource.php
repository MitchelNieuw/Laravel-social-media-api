<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 */
class MessageResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'image' =>  $this->image,
            'user_id' => $this->user_id,
            'createdAt' => $this->created_at,
            'user' => new UserResource($this->user),
            'reactions' => ReactionResource::collection($this->reactions),
        ];
    }
}
