<?php

return [
  'notifications' => [
    'five_star_review' => (bool) env('FIVE_STAR_REVIEW_ENABLED', false),
    'five_star_review_window' => (int) env('FIVE_STAR_REVIEW_WINDOW', 3600),
    'five_star_review_per_window' => (int) env('FIVE_STAR_REVIEW_PER_WINDOW', 5),
    'ride_cancellation' => (bool) env('RIDE_CANCELLATION_ENABLED', false),
  ],

  'zoho_rider_sync' => (bool) env('ZOHO_RIDER_SYNC', false),
  'chatwoot_client_sync' => (bool) env('CHATWOOT_CLIENT_SYNC', false),
];

