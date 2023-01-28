<?php

namespace App\Events\Shared;

use App\Models\Shared\File;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadedFile
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly File $file, public readonly string $path, public readonly string $disk)
    {
    }
}
