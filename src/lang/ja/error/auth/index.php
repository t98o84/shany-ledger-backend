<?php

use App\Actions\Auth\AuthErrorCode;

return [
    AuthErrorCode::Unauthenticated->code() => '認証されていません。',

    AuthErrorCode::Unauthorized->code() => '権限がありません。',

    AuthErrorCode::AlreadyRegisteredEmail->code() => 'メールアドレスが既に登録されています。',

    AuthErrorCode::FileIOFailed->code() => 'ファイルの読み込みもしくは書き込みに失敗しました。',

    AuthErrorCode::InvalidUserId->code() => 'ユーザーIDが無効です。',

    AuthErrorCode::InvalidSignature->code() => '署名が無効です。',

    AuthErrorCode::SignatureExpired->code() => '署名の有効期限が切れています。',

    AuthErrorCode::TokenExpired->code() => 'トークンの有効期限が切れています。',

    AuthErrorCode::InvalidToken->code() => 'トークンが無効です。',

    AuthErrorCode::InvalidEmail->code() => 'メールアドレスが無効です。',

    AuthErrorCode::EmailVerified->code() => 'メールアドレスは既に検証済です。',

    AuthErrorCode::InvalidEmailOrPassword->code() => 'メールアドレスもしくはパスワードが無効です。',

    AuthErrorCode::InvalidPassword->code() => 'パスワードが無効です。',
];
