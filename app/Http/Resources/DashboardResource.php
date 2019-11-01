<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 */
class DashboardResource extends JsonResource
{
    /**
     * @param Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
        ];
    }
}
