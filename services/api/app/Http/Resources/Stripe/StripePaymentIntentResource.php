<?php

namespace App\Http\Resources\Stripe;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StripePaymentIntentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pi = is_array($this->resource) ? $this->resource : [];

        $customerId = $pi['customer'] ?? null;
        $latestChargeId = $pi['latest_charge'] ?? null;

        return [
            'id' => (string) ($pi['id'] ?? ''),
            'customerId' => is_string($customerId) ? $customerId : null,
            'latestChargeId' => is_string($latestChargeId) ? $latestChargeId : null,
        ];
    }
}

