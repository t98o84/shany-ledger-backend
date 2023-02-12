<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Exceptions\ProblemDetails\ForbiddenErrorException;
use App\Exceptions\ProblemDetails\InternalServerErrorException;
use App\Exceptions\ProblemDetails\UnauthorizedErrorException;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;

class AuthErrorCodeTest extends TestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        $this->createApplication();
    }

    public function testErrorCodeProvider_AllErrorCode_AllErrorCodesAreAddedToTheProver()
    {
        $errorCodes = array_map(static fn(array $code) => $code[0], $this->errorCodeProvider());
        foreach (AuthErrorCode::cases() as $errorCode) {
            $this->assertContains($errorCode, $errorCodes);
        }
    }

    /**
     * @dataProvider errorCodeProvider
     */
    public function test_ErrorCode_SameValues(AuthErrorCode $error, string $code, string $title, string|null $detail, string $problemDetail): void
    {
        $this->assertSame($code, $error->code());
        $this->assertSame(__($title), $error->title());
        $this->assertSame(__($detail), $error->detail());
        $this->assertInstanceOf($problemDetail, $error->toProblemDetailException());
    }

    public function errorCodeProvider(): array
    {
        return [
            AuthErrorCode::Unauthenticated->value => [AuthErrorCode::Unauthenticated, AuthErrorCode::Unauthenticated->value, 'error/auth/index.Unauthenticated', null, UnauthorizedErrorException::class],
            AuthErrorCode::Unauthorized->value => [AuthErrorCode::Unauthorized, AuthErrorCode::Unauthorized->value, 'error/auth/index.Unauthorized', null, ForbiddenErrorException::class],
            AuthErrorCode::AlreadyRegisteredEmail->value => [AuthErrorCode::AlreadyRegisteredEmail, AuthErrorCode::AlreadyRegisteredEmail->value, 'error/auth/index.AlreadyRegisteredEmail', null, BadRequestsErrorException::class],
            AuthErrorCode::FileIOFailed->value => [AuthErrorCode::FileIOFailed, AuthErrorCode::FileIOFailed->value, 'error/auth/index.FileIOFailed', null, InternalServerErrorException::class],
            AuthErrorCode::InvalidUserId->value => [AuthErrorCode::InvalidUserId, AuthErrorCode::InvalidUserId->value, 'error/auth/index.InvalidUserId', null, BadRequestsErrorException::class],
            AuthErrorCode::InvalidSignature->value => [AuthErrorCode::InvalidSignature, AuthErrorCode::InvalidSignature->value, 'error/auth/index.InvalidSignature', null, BadRequestsErrorException::class],
            AuthErrorCode::SignatureExpired->value => [AuthErrorCode::SignatureExpired, AuthErrorCode::SignatureExpired->value, 'error/auth/index.SignatureExpired', null, BadRequestsErrorException::class],
            AuthErrorCode::TokenExpired->value => [AuthErrorCode::TokenExpired, AuthErrorCode::TokenExpired->value, 'error/auth/index.TokenExpired', null, BadRequestsErrorException::class],
            AuthErrorCode::InvalidToken->value => [AuthErrorCode::InvalidToken, AuthErrorCode::InvalidToken->value, 'error/auth/index.InvalidToken', null, BadRequestsErrorException::class],
            AuthErrorCode::InvalidEmail->value => [AuthErrorCode::InvalidEmail, AuthErrorCode::InvalidEmail->value, 'error/auth/index.InvalidEmail', null, BadRequestsErrorException::class],
            AuthErrorCode::EmailVerified->value => [AuthErrorCode::EmailVerified, AuthErrorCode::EmailVerified->value, 'error/auth/index.EmailVerified', null, BadRequestsErrorException::class],
            AuthErrorCode::InvalidEmailOrPassword->value => [AuthErrorCode::InvalidEmailOrPassword, AuthErrorCode::InvalidEmailOrPassword->value, 'error/auth/index.InvalidEmailOrPassword', null, BadRequestsErrorException::class],
            AuthErrorCode::InvalidPassword->value => [AuthErrorCode::InvalidPassword, AuthErrorCode::InvalidPassword->value, 'error/auth/index.InvalidPassword', null, BadRequestsErrorException::class],
        ];
    }
}
