<?php

namespace App\Enums;

enum ClientNotificationType: string
{
  case FIVE_STAR_REVIEW = 'five_star_review';
  case CANCELLED = 'cancelled';
}
