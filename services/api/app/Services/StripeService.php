<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StripeService
{
    private function http()
    {
        $secretKey = trim((string) env('STRIPE_SECRET_KEY'));
        if ($secretKey === '') {
            throw new \RuntimeException('Missing STRIPE_SECRET_KEY');
        }

        $req = Http::withToken($secretKey)->acceptJson();

        $accountId = trim((string) env('STRIPE_ACCOUNT_ID'));
        if ($accountId !== '') {
            $req = $req->withHeaders(['Stripe-Account' => $accountId]);
        }

        return $req;
    }

    public function findPaymentIntentByAutofleetPaymentId(string $paymentId): ?array
    {
        $query = "metadata['paymentId']:'" . str_replace("'", "\\'", $paymentId) . "'";
        $url = 'https://api.stripe.com/v1/payment_intents/search';
        $res = $this->http()->get($url, ['query' => $query]);
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

    public function retrieveCharge(string $chargeId): array
    {
        $chargeId = trim($chargeId);
        if ($chargeId === '') {
            throw new \RuntimeException('Missing Stripe charge id');
        }

        $url = 'https://api.stripe.com/v1/charges/' . rawurlencode($chargeId);
        $res = $this->http()->get($url);
        if (!$res->ok()) {
            throw new \RuntimeException('Stripe request failed: ' . $res->body(), $res->status());
        }

        $data = $res->json();
        if (!is_array($data)) {
            throw new \RuntimeException('Stripe response invalid');
        }

        return $data;
    }

    public function dashboardUrlForPaymentIntent(string $paymentIntentId): string
    {
        return "https://dashboard.stripe.com/payments/{$paymentIntentId}";
    }
}
