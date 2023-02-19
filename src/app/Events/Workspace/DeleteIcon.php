<?php

namespace App\Events\Workspace;

use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteIcon
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Workspace $workspaceIcon, public readonly WorkspaceAccount $workspaceAccount)
    {
    }
}
