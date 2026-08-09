<?php

namespace App\Jobs\AutoFleet;

use App\Models\RideAdditionalCharge;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Persists Autofleet `additional-charge-added` webhooks into {@see RideAdditionalCharge}.
 *
 * Webhook shape (camelCase) → DB columns (snake_case):
 *
 * data.id                  - autofleet_charge_id
 * data.priceCalculationId  - price_calculation_id
 * data.businessModelId     - business_model_id
 * data.amount              - amount
 * data.chargeFor           - charge_for
 * data.description         - description
 * data.createdAt           - autofleet_created_at
 * data.updatedAt           - autofleet_updated_at
 * data.deletedAt           - autofleet_deleted_at
 */
class PersistRideAdditionalCharge implements ShouldQueue
{
  use Queueable;

  public function __construct(
    public array $payload
  ) {
  }

  public function handle(): void
  {
    $data = data_get($this->payload, 'data');

    if (! is_array($data) || empty($data['id']) || empty($data['priceCalculationId'])) {
      Log::warning('AF additional charge: missing data', [
        'has_data' => is_array($data),
      ]);

      return;
    }

    $charge = RideAdditionalCharge::create([
      'autofleet_charge_id' => (string) $data['id'],
      'price_calculation_id' => (string) $data['priceCalculationId'],
      'business_model_id' => $this->uuidOrNull(data_get($data, 'businessModelId')),
      'amount' => $this->decimalOrZero(data_get($data, 'amount')),
      'charge_for' => (string) data_get($data, 'chargeFor', ''),
      'description' => $this->stringOrNull(data_get($data, 'description')),
      'autofleet_created_at' => $this->timestampOrNull(data_get($data, 'createdAt')),
      'autofleet_updated_at' => $this->timestampOrNull(data_get($data, 'updatedAt')),
      'autofleet_deleted_at' => $this->timestampOrNull(data_get($data, 'deletedAt')),
    ]);

    Log::info('AF additional charge: persisted', [
      'autofleet_charge_id' => $charge->autofleet_charge_id,
      'price_calculation_id' => $charge->price_calculation_id,
    ]);
  }

  private function uuidOrNull(mixed $value): ?string
  {
    return is_string($value) && $value !== '' && Str::isUuid($value) ? $value : null;
  }

  private function stringOrNull(mixed $value): ?string
  {
    return is_string($value) && $value !== '' ? $value : null;
  }

  private function decimalOrZero(mixed $value): float
  {
    if ($value === null || $value === '') {
      return 0.0;
    }

    return (float) $value;
  }

  private function timestampOrNull(mixed $value): ?Carbon
  {
    if (! is_string($value) || $value === '') {
      return null;
    }

    try {
      return Carbon::parse($value);
    } catch (\Throwable) {
      return null;
    }
  }
}
