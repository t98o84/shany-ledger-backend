<?php

namespace App\Http\Controllers\Ledger;

use App\Actions\Ledger\CreateLedger;
use App\Actions\Ledger\LedgerErrorCode;
use App\Actions\Ledger\UpdateLedger;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ledger\LedgerResource;
use App\Requests\Ledger\CreateLedgerRequest;
use App\Requests\Ledger\UpdateLedgerRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LedgerController extends Controller
{
    /**
     * @throws \Throwable
     * @throws ProblemDetailsException
     */
    public function store(Request $request, CreateLedger $createWorkspace, $workspace): \Illuminate\Http\JsonResponse
    {
        $ledger = $createWorkspace->handle(CreateLedgerRequest::make([...$request->all(), 'workspace_id' => $workspace]));
        if ($ledger instanceof LedgerErrorCode) {
            throw $ledger->toProblemDetailException();
        }

        return response()->json(LedgerResource::make($ledger), 201);
    }


    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function update(Request $request, UpdateLedger $updateLedger, $workspace, $ledger): \Illuminate\Http\JsonResponse
    {
        $ledger = $updateLedger->handle(UpdateLedgerRequest::make([...$request->all(), 'workspace_id' => $workspace, 'id' => $ledger]));
        return response()->json(LedgerResource::make($ledger));
    }
}
