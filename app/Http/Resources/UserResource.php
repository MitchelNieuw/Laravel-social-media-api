<?php

namespace App\Http\Resources;

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
            'tag' => '@'.$this->tag,
            'profilePicture' => url('') . '/profilePictures/' . $this->profilePicture,
            'jwtToken' => $this->jwt_token,
            'createdAt' => $this->created_at,
        ];
    }
}
