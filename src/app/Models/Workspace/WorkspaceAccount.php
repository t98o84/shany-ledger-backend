<?php

namespace App\Models\Workspace;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkspaceAccount extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = [];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function isAdminister(): bool
    {
        return $this->role === WorkspaceAccountRole::Administrator->value;
    }

    public function isEditor(): bool
    {
        return $this->role === WorkspaceAccountRole::Editor->value;
    }

    public function isViewer(): bool
    {
        return $this->role === WorkspaceAccountRole::Viewer->value;
    }

    public function isGuest(): bool
    {
        return $this->role === WorkspaceAccountRole::Guest->value;
    }

    public function isAdministerHigher(): bool
    {
        return $this->isAdminister();
    }

    public function isEditorHigher(): bool
    {
        return  $this->isAdminister() || $this->isEditor();
    }

    public function isViewerHigher(): bool
    {
        return $this->isAdminister() || $this->isEditor() || $this->isViewer();
    }

    public function isGuestHigher(): bool
    {
        return  $this->isAdminister() || $this->isEditor() || $this->isViewer() || $this->isGuest();
    }
}
