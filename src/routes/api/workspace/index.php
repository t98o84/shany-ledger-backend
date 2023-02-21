<?php

Route::name('workspace.')->prefix('workspaces')->middleware(['auth', 'verified'])->group(function () {
    Route::post('/', [\App\Http\Controllers\Workspace\WorkspaceController::class, 'create'])->name('store');
    Route::prefix('{workspace}')->group(function () {
        Route::patch('/', [\App\Http\Controllers\Workspace\WorkspaceController::class, 'update'])->name('update');
        Route::delete('/', [\App\Http\Controllers\Workspace\WorkspaceController::class, 'delete'])->name('delete');

        Route::put('/icon', [\App\Http\Controllers\Workspace\IconController::class, 'update'])->name('update-icon');
        Route::delete('/icon', [\App\Http\Controllers\Workspace\IconController::class, 'delete'])->name('delete-icon');
    });
});
