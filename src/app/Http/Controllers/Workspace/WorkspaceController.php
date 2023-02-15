<?php

namespace App\Http\Controllers\Workspace;

use App\Actions\Workspace\CreateWorkspace;
use App\Actions\Workspace\WorkspaceErrorCode;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\CreateWorkspaceRequest;
use App\Http\Resources\Workspace\WorkspaceResource;

class WorkspaceController extends Controller
{
    /**
     * @throws \Throwable
     * @throws ProblemDetailsException
     */
    public function create(CreateWorkspaceRequest $request, CreateWorkspace $createWorkspace): \Illuminate\Http\JsonResponse
    {
        $workspace = $createWorkspace->handle(
            ownerId: \Auth::id(),
            url: $request->url,
            name: $request->name,
            description: $request->description,
            isPublic: $request->is_public,
        );

        if ($workspace instanceof WorkspaceErrorCode) {
            throw $workspace->toProblemDetailException();
        }

        return response()->json(WorkspaceResource::make($workspace), 201);
    }
}
