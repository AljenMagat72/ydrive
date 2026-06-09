<?php

namespace App\Jobs\AutoFleet;

use App\Enums\RidePriceSnapshotPayoutStatus;
use App\Models\RidePriceSnapshot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Persists Autofleet `ride-price-change` webhooks into {@see RidePriceSnapshot}.
 *
 * Webhook shape (camelCase) - DB columns (snake_case):
 *
 * data.priceCalculation.id                 - price_calculation_id
 * data.priceCalculation.rideId           - ride_id (unique row per ride)
 * data.clientId / data.driverId          - filled later by ride-updated webhook (completed/canceled)
 * data.priceCalculation.pricingPolicyId  - pricing_policy_id
 * data.priceCalculation.businessModelId
 *   or data.businessModelId                - business_model_id
 * data.priceCalculation.currency         - currency
 * data.priceCalculation.calculationReason - calculation_reason
 * data.priceCalculation.basePrice        - base_price (null - 0)
 * data.priceCalculation.surgePrice       - surge_price (null - 0)
 * data.priceCalculation.totalPrice       - total_price (null - 0)
 * data.priceCalculation.totalDriverEarnings - total_driver_earnings (ride-level sum; primary field)
 * data.priceCalculation.driverEarnings   - legacy / partial line; only used if totalDriverEarnings absent
 * data.priceCalculation.items            - JSON line items (discounts, distance, time, etc.)
 */
class PersistRidePriceSnapshot implements ShouldQueue
{
  use Queueable;

  public function __construct(
    public array $payload
  ) {
  }

  public function handle(): void
  {
    $data = data_get($this->payload, 'data');
    $priceCalculation = is_array($data) ? data_get($data, 'priceCalculation') : null;

    if (! is_array($priceCalculation) || empty($priceCalculation['id']) || empty($priceCalculation['rideId'])) {
      Log::warning('AF price snapshot: missing priceCalculation', [
        'has_data' => is_array($data),
      ]);

      return;
    }

    $rideId = (string) $priceCalculation['rideId'];

    $snapshot = RidePriceSnapshot::firstOrNew(['ride_id' => $rideId]);
    $isNew = ! $snapshot->exists;

    $snapshot->price_calculation_id = (string) $priceCalculation['id'];
    $snapshot->business_model_id = $this->uuidOrNull(
      data_get($priceCalculation, 'businessModelId') ?? (is_array($data) ? data_get($data, 'businessModelId') : null)
    );
    $snapshot->pricing_policy_id = $this->uuidOrNull(data_get($priceCalculation, 'pricingPolicyId'));

    $currency = data_get($priceCalculation, 'currency');
    $snapshot->currency = is_string($currency) && $currency !== '' ? Str::limit($currency, 10, '') : null;

    $calculationReason = data_get($priceCalculation, 'calculationReason');
    $snapshot->calculation_reason = is_string($calculationReason) && $calculationReason !== '' ? $calculationReason : null;

    $snapshot->base_price = $this->decimalOrZero(data_get($priceCalculation, 'basePrice'));
    $snapshot->surge_price = $this->decimalOrZero(data_get($priceCalculation, 'surgePrice'));
    $snapshot->total_price = $this->decimalOrZero(data_get($priceCalculation, 'totalPrice'));
    $snapshot->total_driver_earnings = $this->resolveTotalDriverEarnings($priceCalculation);

    $items = data_get($priceCalculation, 'items');
    $snapshot->items = is_array($items) ? $items : [];

    if ($isNew) {
      $snapshot->payout_status = RidePriceSnapshotPayoutStatus::TO_BE_SETTLED;
    }

    $snapshot->save();

    Log::info('AF price snapshot: persisted', [
      'ride_id' => $rideId,
      'price_calculation_id' => $snapshot->price_calculation_id,
      'action' => $isNew ? 'created' : 'updated',
    ]);
  }

  private function resolveTotalDriverEarnings(array $priceCalculation): float
  {
    $total = data_get($priceCalculation, 'totalDriverEarnings');
    if ($total !== null && $total !== '') {
      return (float) $total;
    }

    return $this->decimalOrZero(data_get($priceCalculation, 'driverEarnings'));
  }

  private function uuidOrNull(mixed $value): ?string
  {
    return is_string($value) && $value !== '' && Str::isUuid($value) ? $value : null;
  }

  private function decimalOrZero(mixed $value): float
  {
    if ($value === null || $value === '') {
      return 0.0;
    }

    return (float) $value;
  }
}
