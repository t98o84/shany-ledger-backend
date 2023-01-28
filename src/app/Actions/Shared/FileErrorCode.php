<?php

namespace App\Actions\Shared;

enum FileErrorCode: string
{
    case FileNotExists = 'FileNotExists';

    case FailedToUploadFileToStorage = 'FailedToUploadFileToStorage';

    case FailedToGetFileSize = 'FailedToGetFileSize';


    case RemoveFileFailed = 'RemoveFileFailed';
}
