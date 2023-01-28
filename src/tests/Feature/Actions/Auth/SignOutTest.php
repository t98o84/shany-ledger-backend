<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SignOut;
use App\Actions\Auth\SignUpWithEmailAndPassword;
use App\Events\Auth\SignedOut;
use App\Events\Auth\SignedUp;
use App\Models\Auth\PersonalAccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SignOutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly SignOut $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        $this->action = new SignOut();
    }

    public function testHandle_ValidData_TrueReturned(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('access-token');

        $true = $this->action->handle($token->plainTextToken);

        $this->assertTrue($true);
        $this->assertNull(PersonalAccessToken::findToken($token->plainTextToken));
    }

    public function testHandle_CreateMultipleTokens_OnlySpecifiedTokensAreDeleted(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('access-token');
        $tokenNotDeleted = $user->createToken('access-token');

        $true = $this->action->handle($token->plainTextToken);

        $this->assertTrue($true);
        $this->assertInstanceOf(PersonalAccessToken::class, PersonalAccessToken::findToken($tokenNotDeleted->plainTextToken));
    }

    public function testHandle_ValidData_SignedOutEventDispatched(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('access-token');

        $this->action->handle($token->plainTextToken);

        \Event::assertDispatched(SignedOut::class);
    }

}
