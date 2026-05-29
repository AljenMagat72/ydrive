<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Snapshot of Autofleet price calculation for a ride (driver payout / fare breakdown).
 *
 * @property int $id
 * @property string $ride_id
 * @property string|null $client_id
 * @property string|null $driver_id
 * @property string $price_calculation_id
 * @property string|null $business_model_id
 * @property string|null $pricing_policy_id
 * @property string|null $demand_source_id
 * @property string|null $currency
 * @property string|null $calculation_reason
 * @property string $base_price
 * @property string $surge_price
 * @property string $total_price
 * @property string $total_driver_earnings
 * @property string $payout_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class RidePriceSnapshot extends Model
{
  public const PAYOUT_STATUS_TO_BE_SETTLED = 'to_be_settled';

  public const PAYOUT_STATUS_IN_PROGRESS = 'in_progress';

  public const PAYOUT_STATUS_SETTLED = 'settled';

  protected $fillable = [
    'ride_id',
    'client_id',
    'driver_id',
    'price_calculation_id',
    'business_model_id',
    'pricing_policy_id',
    'demand_source_id',
    'currency',
    'calculation_reason',
    'base_price',
    'surge_price',
    'total_price',
    'total_driver_earnings',
    'payout_status',
  ];

  protected function casts(): array
  {
    return [
      'base_price' => 'decimal:4',
      'surge_price' => 'decimal:4',
      'total_price' => 'decimal:4',
      'total_driver_earnings' => 'decimal:4',
    ];
  }
}
