<?php

use App\Enums\ClientNotificationType;
use App\Enums\RideStates;
use App\Models\ClientNotification;
use App\Notifications\RideCancelledNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
  Config::set('features.notifications.ride_cancellation', true);
});

test('creates notification when ride is cancelled with valid payment method', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $paymentId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::CANCELLED->value,
  ])->count())->toBe(1);

  Notification::assertSentOnDemand(RideCancelledNotification::class);
});

test('prevents duplicate notifications for same cancelled ride', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $paymentId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::CANCELLED->value,
  ])->count())->toBe(1);

  Notification::assertSentOnDemandTimes(RideCancelledNotification::class, 1);
});

test('does not create notification for non-uuid payment methods', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => 'cash',
        ]
      ],
    ]
  ]);

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => 'offline',
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::CANCELLED->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('does not create notification when price amount is not zero', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $paymentId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 10.50,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::CANCELLED->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('does not create notification for non-cancelled ride states', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $paymentId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::COMPLETED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('handles invalid payload gracefully', function () {
  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => 'not-a-uuid',
      'clientId' => 'also-not-a-uuid',
      'state' => RideStates::CANCELED->value,
    ]
  ]);

  $response->assertStatus(422);

  expect(ClientNotification::count())->toBe(0);
  Notification::assertNothingSent();
});

test('handles missing required fields gracefully', function () {
  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => Str::uuid()->toString(),
    ]
  ]);

  $response->assertStatus(422);
  expect(ClientNotification::count())->toBe(0);
  Notification::assertNothingSent();
});

test('does not create notification when both price and payment method are invalid', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 25.00,
      'payment' => [
        'paymentMethod' => [
          'id' => 'cash',
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::CANCELLED->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('handles negative price amounts', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $paymentId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => -10.00,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::CANCELLED->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('different rides can have notifications with same client', function () {
  $clientId = Str::uuid()->toString();
  $rideId1 = Str::uuid()->toString();
  $rideId2 = Str::uuid()->toString();
  $paymentId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId1,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId2,
      'clientId' => $clientId,
      'state' => RideStates::CANCELED->value,
      'priceAmount' => 0,
      'payment' => [
        'paymentMethod' => [
          'id' => $paymentId,
        ]
      ],
    ]
  ]);

  expect(ClientNotification::where('client_id', $clientId)->count())->toBe(2);
  Notification::assertSentOnDemandTimes(RideCancelledNotification::class, 2);
});
