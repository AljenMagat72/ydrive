<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Autofleet additional charge webhook record (e.g. tips).
 *
 * @property int $id
 * @property string $autofleet_charge_id
 * @property string $price_calculation_id
 * @property string|null $business_model_id
 * @property string $amount
 * @property string $charge_for
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $autofleet_created_at
 * @property \Illuminate\Support\Carbon|null $autofleet_updated_at
 * @property \Illuminate\Support\Carbon|null $autofleet_deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class RideAdditionalCharge extends Model
{
  protected $fillable = [
    'autofleet_charge_id',
    'price_calculation_id',
    'business_model_id',
    'amount',
    'charge_for',
    'description',
    'autofleet_created_at',
    'autofleet_updated_at',
    'autofleet_deleted_at',
  ];

  protected function casts(): array
  {
    return [
      'amount' => 'decimal:4',
      'autofleet_created_at' => 'datetime',
      'autofleet_updated_at' => 'datetime',
      'autofleet_deleted_at' => 'datetime',
    ];
  }
}
