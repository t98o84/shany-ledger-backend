<?php

namespace App\Jobs;

enum Queue: string
{
    case Notifications = 'notifications';
    case Files = 'files';
}
