<?php

namespace App\Http\Resources\Ledger;

use App\Http\Resources\Shared\OperatorResource;
use App\Models\Ledger\Ledger;
use App\Models\Ledger\LedgerAggregationSetting;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LedgerAggregationSetting
 */
class LedgerAggregationSettingResource extends JsonResource
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
            'ledger_id' => $this->ledger_id,
            'max_input' => $this->max_input,
            'min_input' => $this->min_input,
            'max_output' => $this->max_output,
            'min_output' => $this->min_output,
            'max_total' => $this->max_total,
            'min_total' => $this->min_total,
            'fixed_point_number' => $this->fixed_point_number,

            'created_at' => $this->when(!is_null($this->created_at), $this->created_at),
            'updated_at' => $this->when(!is_null($this->updated_at), $this->updated_at),
        ];
    }
}
