<?php

namespace App\Http\Controllers\AutoFleet;

use App\Notifications\FiveStarRideNotification;
use App\Notifications\RideCancelledNotification;
use App\Services\Driver\DriverService;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Jobs\AutoFleet\PersistRideAdditionalCharge;
use App\Jobs\AutoFleet\PersistRidePriceSnapshot;
use App\Services\AutoFleetService;
use App\Models\ClientNotification;
use App\Models\Driver;
use App\Models\RidePriceSnapshot;
use App\Enums\ClientNotificationType;
use App\Enums\RideStates;
use Illuminate\Validation\ValidationException;
use Notification;

class AutoFleetWebHookController extends Controller
{
    public function __construct(
        protected AutoFleetService $autoFleetService,
    ) {
    }

    public function driverCreation(Request $request)
    {
        $driver = $request->input('driver');

        app(DriverService::class)->updateOrCreateAutofleetDriver($driver);

        return response()->json(['status' => 'ok']);
    }


    public function rideUpdated(Request $request)
    {
        $this->syncRidePriceSnapshotParticipants($request);

        if (config('features.notifications.ride_cancellation')) {
            $this->handleCancellation($request);
        }
        if (config('features.notifications.five_star_review')) {
            $this->handleCompletionWithReview($request);
        }

        return response()->json(['status' => 'ok']);
    }

    private function syncRidePriceSnapshotParticipants(Request $request): void
    {
        $rideId = $request->input('data.id');
        $state = $request->input('data.state');

        if (! in_array($state, [RideStates::COMPLETED->value, RideStates::CANCELED->value], true)) {
            return;
        }

        if (! is_string($rideId) || ! Str::isUuid($rideId)) {
            return;
        }

        $snapshot = RidePriceSnapshot::where('ride_id', $rideId)->first();

        if ($snapshot === null) {
            Log::info('AF price snapshot: no snapshot to sync participants', [
                'ride_id' => $rideId,
                'state' => $state,
            ]);

            return;
        }

        $clientId = $this->uuidOrNull($request->input('data.clientId'));
        $driverId = $this->uuidOrNull($request->input('data.driverId'));

        $updated = false;

        if ($clientId !== null) {
            $snapshot->client_id = $clientId;
            $updated = true;
        }

        if ($driverId !== null) {
            $snapshot->driver_id = $driverId;
            $updated = true;
        }

        if (! $updated) {
            return;
        }

        $snapshot->save();

        Log::info('AF price snapshot: synced participants', [
            'ride_id' => $rideId,
            'client_id' => $snapshot->client_id,
            'driver_id' => $snapshot->driver_id,
            'state' => $state,
        ]);
    }

    private function uuidOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' && Str::isUuid($value) ? $value : null;
    }

    private function handleCancellation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data.id' => ['required', 'uuid'],
            'data.clientId' => ['required', 'uuid'],
            'data.state' => ['required', 'string'],
            'data.priceAmount' => ['required', 'numeric'],
            'data.payment.paymentMethod.id' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $rideId = $request->input('data.id');
        $clientId = $request->input('data.clientId');
        $state = $request->input('data.state');
        $priceAmount = $request->input('data.priceAmount');
        $paymentMethodId = $request->input('data.payment.paymentMethod.id');

        if ($state !== RideStates::CANCELED->value) {
            return;
        }

        Log::info('ride cancellation: processing', [
            'ride_id' => $rideId,
            'client_id' => $clientId,
            'state' => $state,
        ]);

        if (
            ClientNotification::where([
                'ride_id' => $rideId,
                'client_id' => $clientId,
                'notification_type' => ClientNotificationType::CANCELLED->value
            ])->exists()
        ) {
            Log::info('ride cancellation: already handled', [
                'ride_id' => $rideId,
            ]);
            return;
        }

        if ($priceAmount !== 0 || !Str::isUuid($paymentMethodId)) {
            Log::info('ride cancellation: invalid payment options', [
                'ride_id' => $rideId,
                'price_amount' => $priceAmount,
                'payment_method_id' => $paymentMethodId,
            ]);
            return;
        }

        Log::info('ride cancellation: sending cancellation notification', [
            'ride_id' => $rideId,
        ]);

        ClientNotification::create([
            'ride_id' => $rideId,
            'client_id' => $clientId,
            'notification_type' => ClientNotificationType::CANCELLED->value
        ]);

        $client = $this->autoFleetService->getClientById($clientId);

        Notification::route('twilio', $client['phoneNumber'])
            ->notify(new RideCancelledNotification());
    }

    private function handleCompletionWithReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data.id' => ['required', 'uuid'],
            'data.clientId' => ['required', 'uuid'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $rideId = $request->input('data.id');
        $clientId = $request->input('data.clientId');
        $driverId = $request->input('data.driverId');
        $rating = $request->integer('data.rating');

        $cacheKey = "five_star_notifications";

        if ($rating !== 5) {
            return;
        }

        if (
            ClientNotification::where([
                'client_id' => $clientId,
                'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value
            ])->exists()
        ) {
            Log::info('ride review: client already submitted', [
                'ride_id' => $rideId,
                'client_id' => $clientId,
                'driver_id' => $driverId,
            ]);
            return;
        }

        $ttl = config('features.notifications.five_star_review_window');
        $max = config('features.notifications.five_star_review_per_window');

        $count = Cache::get($cacheKey, 0);
        if ($count >= $max) {
            Log::info('ride review: hit window limit', ['count' => $count]);
            return;
        }

        if ($count === 0) {
            Cache::put($cacheKey, 1, $ttl);
        } else {
            Cache::increment($cacheKey);
        }

        Log::info('ride review: sending notification', ['ride_id' => $rideId, 'count' => $count]);

        ClientNotification::create([
            'client_id' => $clientId,
            'driver_id' => $driverId,
            'ride_id' => $rideId,
            'notification_type' => ClientNotificationType::FIVE_STAR_REVIEW->value
        ]);

        $client = $this->autoFleetService->getClientById($clientId);

        Notification::route('twilio', $client['phoneNumber'])
            ->notify(new FiveStarRideNotification());
    }

    public function priceChange(Request $request)
    {
      $fullPayload = $request->all();
   
      $validator = Validator::make($fullPayload, [
        'data.priceCalculation.id' => ['required', 'uuid'],
        'data.priceCalculation.rideId' => ['required', 'uuid'],
      ]);

      if ($validator->fails()) {
        Log::error('AF price update: invalid payload', [
          'errors' => $validator->errors()->toArray(),
          'payload' => $fullPayload,
        ]);

        throw new \RuntimeException(
          'AutoFleet price change webhook: invalid payload'
        );
      }

      PersistRidePriceSnapshot::dispatchSync($fullPayload);

      return response()->json(['status' => 'ok']);
    }

    public function additionalChargeAdded(Request $request)
    {
      $fullPayload = $request->all();

      $validator = Validator::make($fullPayload, [
        'data.id' => ['required', 'uuid'],
        'data.amount' => ['required', 'numeric'],
        'data.chargeFor' => ['required', 'string'],
        'data.priceCalculationId' => ['required', 'uuid'],
        'data.businessModelId' => ['nullable', 'uuid'],
        'data.description' => ['nullable', 'string'],
      ]);

      if ($validator->fails()) {
        Log::error('AF additional charge: invalid payload', [
          'errors' => $validator->errors()->toArray(),
          'payload' => $fullPayload,
        ]);

        throw new \RuntimeException(
          'AutoFleet additional charge webhook: invalid payload'
        );
      }

      PersistRideAdditionalCharge::dispatchSync($fullPayload);

      return response()->json(['status' => 'ok']);
    }
}
