<?php

namespace App\Models\Workspace;

enum WorkspaceAccountRole: string
{
    case Administrator = 'Administrator';

    case Editor = 'Editor';

    case Viewer = 'Viewer';

    case Guest = 'Guest';
}
