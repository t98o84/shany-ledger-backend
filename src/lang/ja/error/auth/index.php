<?php

use App\Actions\Auth\AuthErrorCode;

return [

    AuthErrorCode::SignUpEmailExists->value => 'メールアドレスが既に登録されています。',

    AuthErrorCode::SignInFailed->value => 'メールアドレスもしくはパスワードに誤りがあります。',

    AuthErrorCode::SendEmailVerificationNotificationUserNotExists->value => '指定されたユーザーが存在しません。',

    AuthErrorCode::SendEmailVerificationNotificationEmailVerified->value => 'メールアドレスは既に検証済です。',

    AuthErrorCode::VerifyEmailUserNotExists->value => '指定されたユーザーが存在しません。',

    AuthErrorCode::VerifyEmailEmailVerified->value => 'メールアドレスは既に検証済です。',

    AuthErrorCode::VerifyEmailInvalidSignature->value => '無効なリクエストです。',

    AuthErrorCode::VerifyEmailSignatureExpired->value => 'リンクの有効期限が切れています。',

    AuthErrorCode::PasswordResetUserNotExists->value => 'メールアドレスに一致するユーザーは存在していません。',
];
