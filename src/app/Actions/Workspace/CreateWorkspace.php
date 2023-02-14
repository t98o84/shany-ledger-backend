<?php

namespace App\Actions\Workspace;

use App\Events\Workspace\CreatedWorkspace;
use App\Models\User;
use App\Models\Workspace\Workspace;

class CreateWorkspace
{
    /**
     * @throws \Throwable
     */
    public function handle(string $ownerId, string $url, string $name, string $description = null, bool $isPublic = false): Workspace|WorkspaceErrorCode
    {
        $user = User::find($ownerId);

        if (is_null($user)) {
            return WorkspaceErrorCode::InvalidUserId;
        }


        \DB::beginTransaction();
        try {
            $createdWorkspaceNum = Workspace::query()->where('owner_id', $ownerId)->lockForUpdate()->count();

            if ($createdWorkspaceNum >= 1) {
                // TODO: プランに応じて上限を変更する
                \DB::rollBack();
                return WorkspaceErrorCode::TooManyWorkspaces;
            }

            $workspace = Workspace::create([
                'id' => (string)\Str::orderedUuid(),
                'owner_id' => $ownerId,
                'url' => $url,
                'name' => $name,
                'description' => $description,
                'is_public' => $isPublic,
            ]);

            CreatedWorkspace::dispatch($workspace);

            \DB::commit();

            return $workspace;
        } catch (\Throwable $throwable) {
            \DB::rollBack();
            throw $throwable;
        }
    }
}
