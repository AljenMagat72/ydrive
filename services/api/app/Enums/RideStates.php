<?php

namespace App\Enums;

enum RideStates: string
{
  case MATCHING = 'matching';
  case PENDING = 'pending';
  case DISPATCHED = 'dispatched';
  case ACTIVE = 'active';
  case COMPLETED = 'completed';
  case REJECTED = 'rejected';
  case FAILED = 'failed';
  case CANCELED = 'canceled';
}
