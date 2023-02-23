<?php

namespace App\Events\Workspace;

use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceIcon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdatedIcon
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly WorkspaceIcon $workspaceIcon, public readonly WorkspaceAccount $workspaceAccount)
    {
    }
}
