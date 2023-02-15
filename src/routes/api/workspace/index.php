<?php

Route::name('workspace.')->prefix('workspaces')->middleware(['auth', 'verified'])->group(function () {
    Route::post('/', [\App\Http\Controllers\Workspace\WorkspaceController::class, 'create'])->name('store');
});
