<?php

use App\Actions\Workspace\WorkspaceErrorCode;

return [
    WorkspaceErrorCode::Unauthenticated->code() => '認証されていません。',

    WorkspaceErrorCode::Unauthorized->code() => '権限がありません。',

    WorkspaceErrorCode::FileIOFailed->code() => 'ファイルの読み込みもしくは書き込みに失敗しました。',

    WorkspaceErrorCode::InvalidUserId->code() => 'ユーザーIDが無効です。',
];
