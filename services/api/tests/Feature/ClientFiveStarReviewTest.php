<?php

use App\Enums\ClientNotificationType;
use App\Models\ClientNotification;
use App\Notifications\FiveStarRideNotification;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\travel;

beforeEach(function () {
  Config::set('features.notifications.five_star_review', true);
});

test('creates notification when client gives five star review', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'driverId' => $driverId,
      'rating' => 5,
    ]
  ]);

  $response->assertStatus(200);

  expect(ClientNotification::where([
    'ride_id' => $rideId,
    'client_id' => $clientId,
    'driver_id' => $driverId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(1);

  Notification::assertSentOnDemand(FiveStarRideNotification::class);
});

test('does not create more than max per window', function () {
  Config::set('features.notifications.five_star_review_per_window', 1);
  Config::set('features.notifications.five_star_review_window', 3600);

  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => Str::uuid()->toString(),
      'clientId' => Str::uuid()->toString(),
      'driverId' => Str::uuid()->toString(),
      'rating' => 5,
    ]
  ]);

  $response->assertStatus(200);

  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => Str::uuid()->toString(),
      'clientId' => Str::uuid()->toString(),
      'driverId' => Str::uuid()->toString(),
      'rating' => 5,
    ]
  ]);

  $response->assertStatus(200);

  expect(ClientNotification::where([
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(1);

  Notification::assertSentOnDemandTimes(FiveStarRideNotification::class, 1);
});

test('resets rate limit after window expires', function () {
  Config::set('features.notifications.five_star_review_per_window', 1);
  Config::set('features.notifications.five_star_review_window', 3600);

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => Str::uuid()->toString(),
      'clientId' => Str::uuid()->toString(),
      'driverId' => Str::uuid()->toString(),
      'rating' => 5,
    ]
  ]);

  expect(ClientNotification::count())->toBe(1);

  travel(Config::get('features.notifications.five_star_review_window') + 1)->seconds();

  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => Str::uuid()->toString(),
      'clientId' => Str::uuid()->toString(),
      'driverId' => Str::uuid()->toString(),
      'rating' => 5,
    ]
  ]);

  $response->assertStatus(200);

  expect(ClientNotification::count())->toBe(2);
  Notification::assertSentOnDemand(FiveStarRideNotification::class);
});

test('does not create notification for ratings below five stars', function () {
  $clientId = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  foreach ([1, 2, 3, 4] as $rating) {
    $this->post('/api/webhook/ride-updated', [
      'data' => [
        'id' => Str::uuid()->toString(),
        'clientId' => $clientId,
        'driverId' => $driverId,
        'rating' => $rating,
      ]
    ]);
  }

  expect(ClientNotification::where([
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('prevents duplicate five star review notifications per client', function () {
  $clientId = Str::uuid()->toString();
  $rideId1 = Str::uuid()->toString();
  $rideId2 = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId1,
      'clientId' => $clientId,
      'driverId' => $driverId,
      'rating' => 5,
    ]
  ]);

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId2,
      'clientId' => $clientId,
      'driverId' => $driverId,
      'rating' => 5,
    ]
  ]);

  expect(ClientNotification::where([
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(1);

  Notification::assertSentOnDemandTimes(FiveStarRideNotification::class, 1);
});

test('allows different clients to each have five star review notification', function () {
  $clientId1 = Str::uuid()->toString();
  $clientId2 = Str::uuid()->toString();
  $rideId1 = Str::uuid()->toString();
  $rideId2 = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId1,
      'clientId' => $clientId1,
      'driverId' => $driverId,
      'rating' => 5,
    ]
  ]);

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId2,
      'clientId' => $clientId2,
      'driverId' => $driverId,
      'rating' => 5,
    ]
  ]);

  expect(ClientNotification::where([
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(2);

  Notification::assertSentOnDemandTimes(FiveStarRideNotification::class, 2);
});

test('handles invalid payload for review gracefully', function () {
  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => 'not-a-uuid',
      'clientId' => 'also-not-a-uuid',
      'rating' => 5,
    ]
  ]);

  $response->assertStatus(422);

  expect(ClientNotification::where([
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('handles missing required fields for review gracefully', function () {
  $response = $this->post('/api/webhook/ride-updated', [
    'data' => [
      'rating' => 5,
    ]
  ]);

  $response->assertStatus(422);

  expect(ClientNotification::where([
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('handles zero rating', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'driverId' => $driverId,
      'rating' => 0,
    ]
  ]);

  expect(ClientNotification::where([
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('handles negative rating', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'driverId' => $driverId,
      'rating' => -1,
    ]
  ]);

  expect(ClientNotification::where([
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('handles rating above five', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'driverId' => $driverId,
      'rating' => 10,
    ]
  ]);

  expect(ClientNotification::where([
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->count())->toBe(0);

  Notification::assertNothingSent();
});

test('stores driver id correctly in notification', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();
  $driverId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'driverId' => $driverId,
      'rating' => 5,
    ]
  ]);

  $notification = ClientNotification::where([
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->first();

  expect($notification->driver_id)->toBe($driverId);

  Notification::assertSentOnDemand(FiveStarRideNotification::class);
});

test('handles missing driver id field', function () {
  $clientId = Str::uuid()->toString();
  $rideId = Str::uuid()->toString();

  $this->post('/api/webhook/ride-updated', [
    'data' => [
      'id' => $rideId,
      'clientId' => $clientId,
      'rating' => 5,
    ]
  ]);

  $notification = ClientNotification::where([
    'client_id' => $clientId,
    'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value,
  ])->first();

  expect($notification)->not->toBeNull();
  expect($notification->driver_id)->toBeNull();

  Notification::assertSentOnDemand(FiveStarRideNotification::class);
});
