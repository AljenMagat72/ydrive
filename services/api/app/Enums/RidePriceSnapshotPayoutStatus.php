<?php

namespace App\Enums;

enum RidePriceSnapshotPayoutStatus: string
{
  case TO_BE_SETTLED = 'to_be_settled';

  case IN_PROGRESS = 'in_progress';

  case SETTLED = 'settled';
}
