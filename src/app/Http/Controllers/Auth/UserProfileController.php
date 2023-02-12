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
    public function update(string $user, UpdateUserProfileRequest $request, UpdateUserProfile $updateUserProfile): \Illuminate\Http\JsonResponse
    {
        $updatedUser = $updateUserProfile->handle(id: $user, name: $request->name, email: $request->email);

        if ($updatedUser instanceof AuthErrorCode) {
            throw $updatedUser->toProblemDetailException();
        }

        return response()->json(UserResource::make($updatedUser));
    }

    public function updateAvatar(string $user, UpdateAvatarRequest $request, UpdateUserAvatar $updateAvatar)
    {
        $avatar = $updateAvatar->handle($user, $request->avatar);

        if ($avatar instanceof AuthErrorCode) {
            throw $avatar->toProblemDetailException();
        }

        return response()->json(['avatar' => $avatar->url()]);
    }

    public function deleteAvatar(string $user, DeleteUserAvatar $deleteAvatar): \Illuminate\Http\Response
    {
        $error = $deleteAvatar->handle($user);

        if ($error instanceof AuthErrorCode) {
            throw $error->toProblemDetailException();
        }

        return response()->noContent();
    }
}
