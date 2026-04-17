<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Throwable;

class StripeController extends Controller
{
    public function __construct(protected StripeService $stripeService)
    {
    }

    public function paymentDashboardUrl(Request $request)
    {
        $validated = $request->validate([
            'paymentId' => ['required', 'string'],
        ]);
        $paymentId = trim((string) ($validated['paymentId'] ?? ''));

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

        $piId = (string) ($pi['id'] ?? '');

        if ($piId === '') {
            return response()->json(['message' => 'Stripe response missing payment intent id'], 502);
        }

        return response()->json([
            'url' => $this->stripeService->dashboardUrlForPaymentIntent($piId),
            'paymentIntentId' => $piId,
        ]);
    }
}
