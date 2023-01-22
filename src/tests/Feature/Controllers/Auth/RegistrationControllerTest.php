<?php

namespace Tests\Feature\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testSignUpWithEmailAndPassword_ValidData_CreatedStatusResponse(): void
    {
        $response = $this->postJson(route('auth.sign-up-with-email-and-password'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertCreated();
    }

    public function testSignUpWithEmailAndPassword_ValidData_ResponseDataWasInTheFormatSpecified(): void
    {
        $response = $this->postJson(route('auth.sign-up-with-email-and-password'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->whereType('access_token', 'string')
            ->has('user', fn(AssertableJson $json) => $json
                ->where('id', fn($id) => \Str::isUuid($id))
                ->where('email', 'test@example.com')
                ->where('name', 'test')
                ->where('avatar', null)
                ->missing('password')
            )
        );
    }
}
