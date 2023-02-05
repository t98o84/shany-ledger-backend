<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\UpdateUserProfile;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Exceptions\ProblemDetails\ForbiddenErrorException;
use App\Exceptions\ProblemDetails\NotFoundErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserProfileRequest;
use App\Http\Resources\Auth\UserResource;

class UserProfileController extends Controller
{
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
}
