<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\DeleteUserAvatar;
use App\Actions\Auth\UpdateUserAvatar;
use App\Actions\Auth\UpdateUserProfile;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Exceptions\ProblemDetails\ForbiddenErrorException;
use App\Exceptions\ProblemDetails\NotFoundErrorException;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Http\Controllers\Controller;
use App\Http\ErrorCodeHandlers\Auth\AuthErrorCodeHandler;
use App\Http\Requests\Auth\UpdateAvatarRequest;
use App\Http\Requests\Auth\UpdateUserProfileRequest;
use App\Http\Resources\Auth\UserResource;

class UserProfileController extends Controller
{
    public function __construct(private readonly AuthErrorCodeHandler $authErrorCodeHandler)
    {
    }

    /**
     * @throws \Throwable
     */
    public function update(string $user, UpdateUserProfileRequest $request, UpdateUserProfile $updateUserProfile): \Illuminate\Http\JsonResponse
    {
        $updatedUser = $updateUserProfile->handle(id: $user, name: $request->name, email: $request->email);

        if ($updatedUser instanceof AuthErrorCode) {
            match ($updatedUser) {
                AuthErrorCode::UserNotExists => throw new NotFoundErrorException(),
                AuthErrorCode::Forbidden => throw new ForbiddenErrorException(),
                default => throw new BadRequestsErrorException($updatedUser->value, $updatedUser->message())
            };
        }

        return response()->json(UserResource::make($updatedUser));
    }

    /**
     * @throws \Throwable
     * @throws ProblemDetailsException
     */
    public function updateAvatar(string $user, UpdateAvatarRequest $request, UpdateUserAvatar $updateAvatar)
    {
        $avatar = $updateAvatar->handle($user, $request->avatar);

        if ($avatar instanceof AuthErrorCode) {
            $this->authErrorCodeHandler->handle($avatar);
        }

        return response()->json(['avatar' => $avatar->url()]);
    }


    /**
     * @throws \Throwable
     * @throws ProblemDetailsException
     */
    public function deleteAvatar(string $user, DeleteUserAvatar $deleteAvatar): \Illuminate\Http\Response
    {
        $error = $deleteAvatar->handle($user);

        if ($error instanceof AuthErrorCode) {
            $this->authErrorCodeHandler->handle($error);
        }

        return response()->noContent();
    }
}
