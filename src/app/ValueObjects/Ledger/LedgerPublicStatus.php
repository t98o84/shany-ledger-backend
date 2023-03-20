<?php

namespace App\ValueObjects\Ledger;


enum LedgerPublicStatus: string
{
    case Anyone = 'anyone';

    case WorkspaceParticipant = 'workspace-participant';

    case LedgerParticipant = 'ledger-participant';
}
