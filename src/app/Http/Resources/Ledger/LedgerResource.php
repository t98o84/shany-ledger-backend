<?php

namespace App\Http\Resources\Ledger;

use App\Http\Resources\Shared\OperatorResource;
use App\Models\Ledger\Ledger;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ledger
 */
class LedgerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'public_status' => $this->public_status->value,
            'anyone_settings' => $this->when($this->public_status === LedgerPublicStatus::Anyone, fn() => [
                'url' => $this->public_status_anyone_setting->url,
                'allow_comments' => $this->public_status_anyone_setting->allow_comments,
                'allow_editing' => $this->public_status_anyone_setting->allow_editing,
                'allow_duplicate' => $this->public_status_anyone_setting->allow_duplicate,
                'expiration_started_at' => $this->public_status_anyone_setting->expiration_started_at,
                'expiration_ended_at' => $this->public_status_anyone_setting->expiration_ended_at,
            ]),

            'unit' => $this->when(!is_null($this->unit), fn() => [
                'symbol' => $this->unit->symbol,
                'display_position' => $this->unit->display_position,
                'description' => $this->unit->description,
            ]),

            'workspace' => [
                'id' => $this->workspace->id,
                'url' => $this->workspace->url,
                'name' => $this->workspace->name,
                'description' => $this->workspace->description,
                'icon' => $this->workspace->icon?->file?->url,
            ],

            'created_at' => $this->when(!is_null($this->created_at), $this->created_at),
            'updated_at' => $this->when(!is_null($this->updated_at), $this->updated_at),
            'deleted_at' => $this->when(!is_null($this->deleted_at), $this->deleted_at),

            'creator' => $this->when(!is_null($this->creator), fn() => OperatorResource::make($this->creator)),
            'updater' => $this->when(!is_null($this->updater), fn() => OperatorResource::make($this->updater)),
            'deleter' => $this->when(!is_null($this->deleter), fn() => OperatorResource::make($this->deleter)),
        ];
    }
}
