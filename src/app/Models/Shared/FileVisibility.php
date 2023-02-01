<?php

namespace App\Models\Shared;

enum FileVisibility: string
{
    case Public = 'public';

    case Private = 'private';
}
