<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 */
class ReactionResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'image' => $this->image,
            'user_id' => $this->user_id,
            'message_id' => $this->message_id,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->user),
        ];
    }
}
