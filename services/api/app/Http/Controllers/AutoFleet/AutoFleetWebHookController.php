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
use App\Services\AutoFleetService;
use App\Models\ClientNotification;
use App\Models\Driver;
use App\Enums\ClientNotificationType;
use App\Enums\RideStates;
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
        if (config('features.notifications.ride_cancellation')) {
            $this->handleCancellation($request);
        }
        if (config('features.notifications.five_star_review')) {
            $this->handleCompletionWithReview($request);
        }

        return response()->json(['status' => 'ok']);
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
            Log::error('ride cancellation: invalid payload', [
                'errors' => $validator->errors()->toArray(),
                'payload' => $request->input('data'),
            ]);
            return;
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
            Log::error('ride review: invalid payload', [
                'errors' => $validator->errors()->toArray(),
                'payload' => $request->input('data'),
            ]);

            return;
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

        if ($count === $max) {
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
}
