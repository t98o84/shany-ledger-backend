<?php

namespace App\Actions\Shared;

enum FileErrorCode: string
{
    case FileAlreadyExists = 'FileAlreadyExists';

    case FileNotExists = 'FileNotExists';

    case FailedToUploadFileToStorage = 'FailedToUploadFileToStorage';

    case FailedToGetFileSize = 'FailedToGetFileSize';

    case RemoveFileFailed = 'RemoveFileFailed';
}
