<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'content' => $this->resource->content,
            'image' => $this->resource->image,
            'user_id' => $this->resource->user_id,
            'message_id' => $this->resource->message_id,
            'created_at' => $this->resource->created_at,
            'user' => new UserResource($this->resource->user),
        ];
    }
}
