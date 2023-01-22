<?php

use App\Actions\Auth\AuthErrorCode;

return [

    AuthErrorCode::SignUpEmailExists->value => 'メールアドレスが既に登録されています。',

    AuthErrorCode::SignInFailed->value => 'メールアドレスもしくはパスワードに誤りがあります。'

];
