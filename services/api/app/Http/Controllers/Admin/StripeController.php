<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Stripe\StripeChargeResource;
use App\Http\Resources\Stripe\StripePaymentIntentResource;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Throwable;

class StripeController extends Controller
{
    public function __construct(protected StripeService $stripeService)
    {
    }

    public function paymentIntent(string $paymentId)
    {
        $paymentId = trim($paymentId);
        if ($paymentId === '') {
            return response()->json(['message' => 'Missing paymentId'], 400);
        }

        try {
            $pi = $this->stripeService->findPaymentIntentByAutofleetPaymentId($paymentId);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Stripe request failed',
                'details' => $e->getMessage(),
            ], 502);
        }

        if ($pi === null) {
            return response()->json(['message' => 'Stripe payment intent not found for paymentId'], 404);
        }

        return response()->json([
            'paymentIntent' => StripePaymentIntentResource::make($pi),
        ]);
    }

    public function charge(string $chargeId)
    {
        $chargeId = trim($chargeId);
        if ($chargeId === '') {
            return response()->json(['message' => 'Missing chargeId'], 400);
        }

        try {
            $charge = $this->stripeService->retrieveCharge($chargeId);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Stripe request failed',
                'details' => $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'charge' => StripeChargeResource::make($charge),
        ]);
    }
}
