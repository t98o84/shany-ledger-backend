<?php

namespace App\Models\Workspace;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

enum WorkspacePublicationSettingState: string
{
    case Public = 'Public';

    case Private = 'Private';
}
