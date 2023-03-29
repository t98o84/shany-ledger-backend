<?php

namespace App\Requests\Ledger;

use App\Models\Ledger\Ledger;
use App\Models\Ledger\LedgerPublicStatusAnyoneSetting;
use App\Models\User;
use App\Models\Workspace\WorkspaceAccount;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class UpdateLedgerPublicStatusRequest
{
    public readonly Ledger $ledger;

    public readonly ?LedgerPublicStatusAnyoneSetting $ledgerPublicStatusAnyoneSetting;

    public readonly WorkspaceAccount $workspaceAccount;

    public readonly User $user;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function __construct(array $params)
    {
        $this->ledger = Ledger::where('workspace_id', $params['workspace_id'])->findOrFail($params['id']);
        $this->authorize();
        $validatedParams = $this->validate($params);
        $this->ledger->fill(\Arr::except($validatedParams, ['anyone_settings']));
        $this->ledgerPublicStatusAnyoneSetting = $this->ledger->public_status === LedgerPublicStatus::Anyone
            ? LedgerPublicStatusAnyoneSetting::findOrNew($this->ledger->id)->fill($validatedParams['anyone_settings'])
            : null;

        if ($this->ledgerPublicStatusAnyoneSetting) {
            $this->ledgerPublicStatusAnyoneSetting->ledger_id = $this->ledger->id;
        }
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
        $workspaceAccount = $this->ledger->workspace->findAccount($this->user->id);
        \Gate::authorize('update', [$this->ledger, $this->ledger->workspace, $workspaceAccount]);
        $this->workspaceAccount = $workspaceAccount;
    }

    /**
     * @throws ValidationException
     */
    private function validate(array $params): array
    {
        $anyoneRules = [];
        foreach (\Arr::only(LedgerPublicStatusAnyoneSetting::make()->validationRules(), [
            'url',
            'allow_comments',
            'allow_editing',
            'allow_duplicate',
            'expiration_started_at',
            'expiration_ended_at',
        ]) as $key => $rule) {
            $anyoneRules["anyone_settings.$key"] = \Arr::map($rule, fn($rule) => $rule === 'required'
                ? 'required_if:public_status,' . LedgerPublicStatus::Anyone->value
                : $rule
            );
        }

        return validator(
            $params,
            [
                ...\Arr::only(
                    $this->ledger->validationRUles(),
                    ['public_status']
                ),
                ...$anyoneRules,
            ]
        )->validate();
    }
}
