<?php

use App\Actions\Auth\AuthErrorCode;

return [
    AuthErrorCode::InvalidRequest->value => '権限がありません。',

    AuthErrorCode::InvalidRequest->value => '無効なリクエストです。',

    AuthErrorCode::SignUpEmailExists->value => 'メールアドレスが既に登録されています。',

    AuthErrorCode::SignInFailed->value => 'メールアドレスもしくはパスワードに誤りがあります。',

    AuthErrorCode::SendEmailVerificationNotificationUserNotExists->value => '指定されたユーザーが存在しません。',

    AuthErrorCode::SendEmailVerificationNotificationEmailVerified->value => 'メールアドレスは既に検証済です。',

    AuthErrorCode::VerifyEmailUserNotExists->value => '指定されたユーザーが存在しません。',

    AuthErrorCode::VerifyEmailEmailVerified->value => 'メールアドレスは既に検証済です。',

    AuthErrorCode::VerifyEmailInvalidSignature->value => '無効なリクエストです。',

    AuthErrorCode::VerifyEmailSignatureExpired->value => 'リンクの有効期限が切れています。',

    AuthErrorCode::SendPasswordResetLinkUserNotExists->value => 'メールアドレスに一致するユーザーは存在していません。',

    AuthErrorCode::ResetPasswordInvalidRequest->value => '無効なリクエストです。',

    AuthErrorCode::ResetPasswordTokenExpired->value => '有効期限が切れています。',
];
