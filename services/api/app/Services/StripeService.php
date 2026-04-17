<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StripeService
{
    public function findPaymentIntentByAutofleetPaymentId(string $paymentId): ?array
    {
        $secretKey = trim((string) env('STRIPE_SECRET_KEY'));
        if ($secretKey === '') {
            throw new \RuntimeException('Missing STRIPE_SECRET_KEY');
        }

        $query = "metadata['paymentId']:'" . str_replace("'", "\\'", $paymentId) . "'";
        $url = 'https://api.stripe.com/v1/payment_intents/search';

        $req = Http::withToken($secretKey)->acceptJson();

        $accountId = trim((string) env('STRIPE_ACCOUNT_ID'));
        if ($accountId !== '') {
            $req = $req->withHeaders(['Stripe-Account' => $accountId]);
        }

        $res = $req->get($url, ['query' => $query]);
        if (!$res->ok()) {
            throw new \RuntimeException('Stripe request failed: ' . $res->body(), $res->status());
        }

        $data = $res->json();
        if (!is_array($data) || !isset($data['data']) || !is_array($data['data']) || count($data['data']) === 0) {
            return null;
        }

        $items = array_values($data['data']);
        for ($i = count($items) - 1; $i >= 0; $i--) {
            $item = $items[$i];
            if (is_array($item) && !empty($item['id'])) {
                return $item;
            }
        }

        return null;
    }

    public function dashboardUrlForPaymentIntent(string $paymentIntentId): string
    {
        return "https://dashboard.stripe.com/payments/{$paymentIntentId}";
    }
}
