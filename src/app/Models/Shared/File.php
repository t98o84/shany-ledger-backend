<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];
}
