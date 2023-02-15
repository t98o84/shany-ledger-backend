<?php

namespace App\Http\Resources\Workspace;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
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
            'owner_id' => $this->owner_id,
            'url' => $this->url,
            'name' => $this->name,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'icon' => $this->icon?->file?->url,
        ];
    }
}
