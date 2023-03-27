<?php

namespace App\Events\Ledger;

use App\Models\Ledger\Ledger;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LedgerUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Ledger $ledger, public readonly WorkspaceAccount $workspaceAccount)
    {
    }
}
