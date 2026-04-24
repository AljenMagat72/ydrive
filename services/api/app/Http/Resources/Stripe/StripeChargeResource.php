<?php

namespace App\Http\Resources\Stripe;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StripeChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ch = is_array($this->resource) ? $this->resource : [];

        $currency = $ch['currency'] ?? null;
        $customerId = $ch['customer'] ?? null;

        return [
            'id' => (string) ($ch['id'] ?? ''),
            'amount' => $ch['amount'] ?? 0,
            'amountCaptured' => $ch['amount_captured'] ?? 0,
            'amountRefunded' => $ch['amount_refunded'] ?? 0,
            'currency' => is_string($currency) ? $currency : null,
            'customerId' => is_string($customerId) ? $customerId : null,
        ];
    }
}

