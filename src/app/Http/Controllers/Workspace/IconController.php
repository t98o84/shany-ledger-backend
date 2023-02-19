<?php

namespace App\Http\Controllers\Workspace;

use App\Actions\Workspace\DeleteIcon;
use App\Actions\Workspace\UpdateIcon;
use App\Actions\Workspace\WorkspaceErrorCode;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\UpdateIconRequest;

class IconController extends Controller
{
    /**
     * @throws \Throwable
     * @throws ProblemDetailsException
     */
    public function update(string $workspace, UpdateIconRequest $request, UpdateIcon $updateIcon): \Illuminate\Http\JsonResponse
    {
        $icon = $updateIcon->handle(
            userId: \Auth::id(),
            workspaceId: $workspace,
            uploadedIcon: $request->icon,
        );

        if ($icon instanceof WorkspaceErrorCode) {
            throw $icon->toProblemDetailException();
        }

        return response()->json(['icon' => $icon->url()]);
    }

    /**
     * @throws \Throwable
     * @throws ProblemDetailsException
     */
    public function delete(string $workspace, DeleteIcon $deleteIcon)
    {
        $error = $deleteIcon->handle(
            userId: \Auth::id(),
            workspaceId: $workspace,
        );

        if ($error instanceof WorkspaceErrorCode) {
            throw $error->toProblemDetailException();
        }

        return response()->noContent();
    }
}
