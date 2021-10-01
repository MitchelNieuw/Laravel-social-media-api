<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'content' => $this->resource->content,
            'image' =>  $this->resource->image,
            'user_id' => $this->resource->user_id,
            'createdAt' => $this->resource->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'reactions' => ReactionResource::collection($this->whenLoaded('reactions')),
        ];
    }
}
