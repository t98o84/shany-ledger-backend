<?php

namespace App\Http\Resources\Ledger;

use App\Http\Resources\Shared\OperatorResource;
use App\Models\Ledger\Ledger;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ledger
 */
class LedgerPublicStatusResource extends JsonResource
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
            'status' => $this->public_status->value,
            'anyone_settings' => $this->when($this->public_status === LedgerPublicStatus::Anyone, fn() => [
                'url' => $this->public_status_anyone_setting->url,
                'allow_comments' => $this->public_status_anyone_setting->allow_comments,
                'allow_editing' => $this->public_status_anyone_setting->allow_editing,
                'allow_duplicate' => $this->public_status_anyone_setting->allow_duplicate,
                'expiration_started_at' => $this->public_status_anyone_setting->expiration_started_at,
                'expiration_ended_at' => $this->public_status_anyone_setting->expiration_ended_at,
            ]),
        ];
    }
}
