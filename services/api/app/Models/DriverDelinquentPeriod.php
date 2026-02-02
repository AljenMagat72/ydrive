<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property-read \App\Models\Driver|null $driver
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod query()
 * @mixin \Eloquent
 */
class DriverDelinquentPeriod extends Model
{
  protected $fillable = [
    'driver_id',
    'started_at',
    'ended_at',
  ];

  public function driver()
  {
    return $this->belongsTo(Driver::class);
  }
}
