<?php

namespace App\Http\Resources\Shared;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class OperatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
