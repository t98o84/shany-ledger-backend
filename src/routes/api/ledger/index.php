<?php

Route::name('ledger.')->prefix('workspaces/{workspace}/ledgers')->middleware(['auth', 'verified'])->group(function () {
    Route::post('/', [\App\Http\Controllers\Ledger\LedgerController::class, 'store'])->name('store');
    Route::prefix('{ledger}')->group(function () {
        Route::patch('/', [\App\Http\Controllers\Ledger\LedgerController::class, 'update'])->name('update');
    });
});
