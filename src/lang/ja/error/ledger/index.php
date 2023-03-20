<?php

use App\Actions\Ledger\LedgerErrorCode;

return [
    LedgerErrorCode::Unauthenticated->code() => '認証されていません。',

    LedgerErrorCode::Unauthorized->code() => '権限がありません。',

    LedgerErrorCode::FileIOFailed->code() => 'ファイルの読み込みもしくは書き込みに失敗しました。',

    LedgerErrorCode::InvalidUserId->code() => 'ユーザーIDが無効です。',

    LedgerErrorCode::InvalidLedgerId->code() => '台帳のIDが無効です。',
];
