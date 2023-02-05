<?php

namespace App\Actions\Auth;

enum AuthErrorCode: string
{
    case Forbidden = 'Forbidden';
    case InvalidRequest = 'InvalidRequest';
    case UserNotExists = 'UserNotExists';
    case FileUploadFailed = 'FileUploadFailed';
    case PasswordMismatch = 'PasswordMismatch';
    case SignUpEmailExists = 'SignUpEmailExists';
    case SignInFailed = 'SignInFailed';
    case SendEmailVerificationNotificationUserNotExists = 'SendEmailVerificationNotificationUserNotExists';
    case SendEmailVerificationNotificationEmailVerified = 'SendEmailVerificationNotificationEmailVerified';
    case VerifyEmailUserNotExists = 'VerifyEmailUserNotExists';
    case VerifyEmailEmailVerified = 'VerifyEmailEmailVerified';
    case VerifyEmailInvalidSignature = 'VerifyEmailInvalidSignature';
    case VerifyEmailSignatureExpired = 'VerifyEmailSignatureExpired';
    case SendPasswordResetLinkUserNotExists = 'SendPasswordResetLinkUserNotExists';
    case ResetPasswordInvalidRequest = 'ResetPasswordInvalidRequest';
    case ResetPasswordTokenExpired = 'ResetPasswordTokenExpired';

    public function message(): string
    {
        return __("error/auth/index.$this->value");
    }
}
