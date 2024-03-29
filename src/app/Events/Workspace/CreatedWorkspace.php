<?php

namespace App\Events\Workspace;

use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatedWorkspace
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Workspace $workspace, public readonly WorkspaceAccount $workspaceAccount)
    {
    }
}
