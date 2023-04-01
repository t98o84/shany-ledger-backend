<?php

namespace App\Requests\Ledger;

use App\Models\Ledger\Ledger;
use App\Models\Ledger\LedgerAggregationSetting;
use App\Models\Ledger\LedgerDetailSetting;
use App\Models\Ledger\LedgerType;
use App\Models\Ledger\LedgerUnit;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class CreateLedgerRequest
{
    public readonly Ledger $ledger;

    public readonly LedgerUnit $ledgerUnit;

    public readonly ?LedgerDetailSetting $detailSetting;

    public readonly Workspace $workspace;

    public readonly WorkspaceAccount $workspaceAccount;

    public readonly User $user;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function __construct(array $params)
    {
        $this->workspace = Workspace::findOrFail($params['workspace_id']);

        $this->authorize();
        $this->validate($params);
    }

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public static function make(array $params): static
    {
        return new static($params);
    }

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    private function authorize(): void
    {
        $this->user = \Auth::authenticate();
        $workspaceAccount = $this->workspace->findAccount($this->user->id);
        \Gate::authorize('store', [Ledger::make(), $this->workspace, $workspaceAccount]);
        $this->workspaceAccount = $workspaceAccount;
    }

    /**
     * @throws ValidationException
     */
    private function validate(array $params)
    {
        $rules = \Arr::except(Ledger::make()->validationRUles(), ['id', 'workspace_id']);
        foreach (\Arr::except(LedgerUnit::make()->validationRUles(), ['ledger_id']) as $key => $rule) {
            $rules["unit.$key"] = $rule;
        }

        $rules = $this->addDetailSettingRules($rules, $params);

        $validated = validator($params, $rules)->validate();

        $this->ledger = Ledger::make(\Arr::except($validated, ['unit', 'detail_setting']));
        $this->ledgerUnit = LedgerUnit::make($validated['unit']);
        $this->detailSetting = $this->ledger->detailSetting()->make($validated['detail_setting'] ?? []);
    }

    private function addDetailSettingRules(array $rules, array $params): array
    {
        if (!isset($params['type'])) {
            return $rules;
        }

        $class = LedgerType::tryFrom($params['type'])?->detailSettingModel();

        if(is_null($class)) {
            return $rules;
        }

        foreach (\Arr::except($class::make()->validationRules(), ['ledger_id']) as $key => $rule) {
            $rules["detail_setting.$key"] = $rule;
        }

        return $rules;
    }
}
