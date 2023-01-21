<?php

namespace App\Actions\Auth;

enum AuthErrorCode: string
{
    case SignUpEmailExists = 'SignUpEmailExists';
    case SignInFailed = 'SignInFailed';
}
