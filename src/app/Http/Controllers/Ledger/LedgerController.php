<?php

namespace App\Http\Controllers\Ledger;

use App\Actions\Ledger\CreateLedger;
use App\Actions\Ledger\LedgerErrorCode;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ledger\LedgerResource;
use App\Requests\Ledger\CreateLedgerRequest;
use Illuminate\Http\Request;

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
}
