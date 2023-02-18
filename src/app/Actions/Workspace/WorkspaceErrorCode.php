<?php

namespace App\Actions\Workspace;

use App\Actions\ErrorCode;
use App\Actions\HasErrorCode;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Exceptions\ProblemDetails\ForbiddenErrorException;
use App\Exceptions\ProblemDetails\InternalServerErrorException;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Exceptions\ProblemDetails\UnauthorizedErrorException;

enum WorkspaceErrorCode: string implements ErrorCode
{
    use HasErrorCode;

    case Unauthenticated = 'Unauthenticated';

    case Unauthorized = 'Unauthorized';

    case AlreadyRegisteredEmail = 'AlreadyRegisteredEmail';

    case FileIOFailed = 'FileIOFailed';

    case InvalidUserId = 'InvalidUserId';

    case InvalidWorkspaceId = 'InvalidWorkspaceId';

    case TooManyWorkspaces = 'TooManyWorkspaces';

    public function messageBaseKey(): string
    {
        return 'error/workspace/index';
    }

    public function toProblemDetailException(): ProblemDetailsException
    {
        return match ($this) {
            self::Unauthenticated => new UnauthorizedErrorException(title: $this->title(), detail: $this->detail()),
            self::Unauthorized => new ForbiddenErrorException(title: $this->title(), detail: $this->detail()),
            self::FileIOFailed => new InternalServerErrorException(title: $this->title(), detail: $this->detail()),
            default => new BadRequestsErrorException(errorCode: $this->code(), title: $this->title(), detail: $this->detail())
        };
    }
}
