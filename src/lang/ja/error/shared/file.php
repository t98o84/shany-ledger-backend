<?php

use App\Actions\Shared\FileErrorCode;

return [
    FileErrorCode::FailedToGetFileSize->value => 'ファイルサイズが不明です。',

    FileErrorCode::FailedToUploadFileToStorage->value => 'ファイルのアップロードに失敗しました。',
];
