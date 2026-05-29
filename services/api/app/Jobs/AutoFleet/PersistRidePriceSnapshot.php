<?php

namespace App\Jobs\AutoFleet;

use App\Models\RidePriceSnapshot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Persists Autofleet `ride-price-change` webhooks into {@see RidePriceSnapshot}.
 *
 * Webhook shape (camelCase) - DB columns (snake_case):
 *
 * data.priceCalculation.id                 - price_calculation_id
 * data.priceCalculation.rideId           - ride_id
 * data.priceCalculation.driverId         - driver_id (optional; also data.driverId)
 * data.priceCalculation.pricingPolicyId  - pricing_policy_id
 * data.priceCalculation.businessModelId
 *   or data.businessModelId                - business_model_id
 * data.priceCalculation.demandSourceId   - demand_source_id
 * data.priceCalculation.currency         - currency
 * data.priceCalculation.calculationReason - calculation_reason
 * data.priceCalculation.basePrice        - base_price (null - 0)
 * data.priceCalculation.surgePrice       - surge_price (null - 0)
 * data.priceCalculation.totalPrice       - total_price (null - 0)
 * data.priceCalculation.totalDriverEarnings (ride-level; preferred when set)
 *   else data.priceCalculation.driverEarnings - total_driver_earnings
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
    $data = Arr::get($this->payload, 'data');
    $pc = is_array($data) ? Arr::get($data, 'priceCalculation') : null;

    if (! is_array($pc) || empty($pc['id']) || empty($pc['rideId'])) {
      Log::warning('AF price snapshot: missing priceCalculation', [
        'has_data' => is_array($data),
      ]);

      return;
    }

    $rideId = (string) $pc['rideId'];
    $driverId = $this->nullableUuid(
      Arr::get($pc, 'driverId') ?? (is_array($data) ? Arr::get($data, 'driverId') : null)
    );

    $snapshot = RidePriceSnapshot::firstOrNew(['ride_id' => $rideId]);

    $snapshot->ride_id = $rideId;
    $snapshot->price_calculation_id = (string) $pc['id'];
    $snapshot->business_model_id = $this->nullableUuid(
      Arr::get($pc, 'businessModelId') ?? (is_array($data) ? Arr::get($data, 'businessModelId') : null)
    );
    $snapshot->pricing_policy_id = $this->nullableUuid(Arr::get($pc, 'pricingPolicyId'));
    $snapshot->demand_source_id = $this->nullableUuid(Arr::get($pc, 'demandSourceId'));
    $snapshot->currency = $this->nullableShortString(Arr::get($pc, 'currency'), 10);
    $snapshot->calculation_reason = $this->nullableString(Arr::get($pc, 'calculationReason'));
    $snapshot->base_price = $this->decimalOrZero(Arr::get($pc, 'basePrice'));
    $snapshot->surge_price = $this->decimalOrZero(Arr::get($pc, 'surgePrice'));
    $snapshot->total_price = $this->decimalOrZero(Arr::get($pc, 'totalPrice'));
    $snapshot->total_driver_earnings = $this->resolveTotalDriverEarnings($pc);

    if ($driverId !== null) {
      $snapshot->driver_id = $driverId;
    }

    if (! $snapshot->exists) {
      $snapshot->payout_status = RidePriceSnapshot::PAYOUT_STATUS_TO_BE_SETTLED;
    }

    $snapshot->save();
  }

  /**
   * Ride-level driver payout: use `totalDriverEarnings` when Autofleet sends it;
   * otherwise `driverEarnings`. Both refer to the same ride (no aggregation from `items`).
   */
  private function resolveTotalDriverEarnings(array $pc): float
  {
    if (array_key_exists('totalDriverEarnings', $pc) && $pc['totalDriverEarnings'] !== null && $pc['totalDriverEarnings'] !== '') {
      return (float) $pc['totalDriverEarnings'];
    }

    return $this->decimalOrZero(Arr::get($pc, 'driverEarnings'));
  }

  private function nullableUuid(mixed $value): ?string
  {
    if (! is_string($value) || $value === '') {
      return null;
    }

    return Str::isUuid($value) ? $value : null;
  }

  private function nullableString(mixed $value): ?string
  {
    if ($value === null) {
      return null;
    }

    if (! is_string($value)) {
      return null;
    }

    return $value === '' ? null : $value;
  }

  private function nullableShortString(mixed $value, int $maxLen): ?string
  {
    $s = $this->nullableString($value);
    if ($s === null) {
      return null;
    }

    return Str::limit($s, $maxLen, '');
  }

  private function decimalOrZero(mixed $value): float
  {
    if ($value === null || $value === '') {
      return 0.0;
    }

    return (float) $value;
  }
}
