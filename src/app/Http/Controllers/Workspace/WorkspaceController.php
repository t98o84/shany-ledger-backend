<?php

namespace App\Http\Controllers\Workspace;

use App\Actions\Workspace\CreateWorkspace;
use App\Actions\Workspace\DeleteWorkspace;
use App\Actions\Workspace\UpdateWorkspace;
use App\Actions\Workspace\WorkspaceErrorCode;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\CreateWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\Workspace\WorkspaceAccountResource;
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

        return response()->json(
            [
                'workspace' => WorkspaceResource::make($workspace['workspace']),
                'workspace_account' => WorkspaceAccountResource::make($workspace['workspace_account'])
            ],
            201
        );
    }

    public function update(string $workspace, UpdateWorkspaceRequest $request, UpdateWorkspace $updateWorkspace)
    {
        $updatedWorkspace = $updateWorkspace->handle(
            userId: \Auth::id(),
            workspaceId: $workspace,
            url: $request->input('url'),
            name: $request->input('name'),
            description: $request->input('description'),
            isPublic: $request->input('is_public'),
        );

        if ($updatedWorkspace instanceof WorkspaceErrorCode) {
            throw $updatedWorkspace->toProblemDetailException();
        }

        return response()->noContent();
    }

    public function delete(string $workspace, DeleteWorkspace $deleteWorkspace)
    {
        $error = $deleteWorkspace->handle(\Auth::id(), $workspace);

        if ($error instanceof WorkspaceErrorCode) {
            throw $error->toProblemDetailException();
        }

        return response()->noContent();
    }
}
