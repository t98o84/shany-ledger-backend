<?php

namespace Tests\Feature\Resources\Ledger;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class LedgerResource
{
    public static function json(User $user): \Closure
    {
        return fn(AssertableJson $json) => $json
            ->where('id', $user->id)
            ->where('name', $user->name)
            ->etc();
    }
}
