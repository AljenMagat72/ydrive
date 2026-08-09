<?php

namespace App\Models;

use App\Enums\RidePriceSnapshotPayoutStatus;
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
 * @property string|null $currency
 * @property string|null $calculation_reason
 * @property string $base_price
 * @property string $surge_price
 * @property string $total_price
 * @property string $total_driver_earnings
 * @property array|null $items
 * @property RidePriceSnapshotPayoutStatus $payout_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class RidePriceSnapshot extends Model
{
  protected $fillable = [
    'ride_id',
    'client_id',
    'driver_id',
    'price_calculation_id',
    'business_model_id',
    'pricing_policy_id',
    'currency',
    'calculation_reason',
    'base_price',
    'surge_price',
    'total_price',
    'total_driver_earnings',
    'items',
    'payout_status',
  ];

  protected function casts(): array
  {
    return [
      'base_price' => 'decimal:4',
      'surge_price' => 'decimal:4',
      'total_price' => 'decimal:4',
      'total_driver_earnings' => 'decimal:4',
      'items' => 'array',
      'payout_status' => RidePriceSnapshotPayoutStatus::class,
    ];
  }
}
