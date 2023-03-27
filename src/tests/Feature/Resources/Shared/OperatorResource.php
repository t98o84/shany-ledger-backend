<?php

namespace Tests\Feature\Resources\Shared;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class OperatorResource
{
    public static function json(User $user): \Closure
    {
        return fn(AssertableJson $json) => $json
            ->where('id', $user->id)
            ->where('name', $user->name)
            ->etc();
    }
}
