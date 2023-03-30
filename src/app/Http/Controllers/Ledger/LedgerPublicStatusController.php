<?php

namespace App\Http\Controllers\Ledger;

use App\Actions\Ledger\UpdateLedgerPublicStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ledger\LedgerPublicStatusResource;
use App\Requests\Ledger\UpdateLedgerPublicStatusRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LedgerPublicStatusController extends Controller
{
    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function update(Request $request, UpdateLedgerPublicStatus $updateLedger, $workspace, $ledger): \Illuminate\Http\JsonResponse
    {
        $ledger = $updateLedger->handle(UpdateLedgerPublicStatusRequest::make([...$request->all(), 'workspace_id' => $workspace, 'id' => $ledger]));
        return response()->json(LedgerPublicStatusResource::make($ledger));
    }
}
