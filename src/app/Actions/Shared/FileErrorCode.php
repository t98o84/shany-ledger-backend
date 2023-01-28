<?php

namespace App\Actions\Shared;

enum FileErrorCode: string
{
    case FailedToUploadFileToStorage = 'FailedToUploadFileToStorage';

    case FailedToGetFileSize = 'FailedToGetFileSize';
}
