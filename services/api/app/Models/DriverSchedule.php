<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $driver_id
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property-read \App\Models\Driver $driver
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DriverSchedule extends Model
{
  protected $fillable = [
    'driver_id',
    'starts_at',
    'ends_at',
  ];

  protected $casts = [
    'starts_at' => 'datetime',
    'ends_at' => 'datetime',
  ];

  protected static function booted(): void
  {
    static::created(function (DriverSchedule $schedule) {
      $driver = $schedule->driver;
      $date = $schedule->starts_at->toDateString();

      Log::info("Driver $driver->first_name $driver->last_name created $date", [
        'id' => $schedule->id,
        'driver_id' => $schedule->driver_id,
        'starts_at' => $schedule->starts_at?->toDateTimeString(),
        'ends_at' => $schedule->ends_at?->toDateTimeString(),
        'created_at' => $schedule->created_at?->toDateTimeString(),
        'updated_at' => $schedule->updated_at?->toDateTimeString(),
      ]);
    });

    static::updated(function (DriverSchedule $schedule) {
      $driver = $schedule->driver;
      $date = $schedule->starts_at->toDateString();

      Log::info("Driver $driver->first_name $driver->last_name updated $date", [
        'id' => $schedule->id,
        'original' => $schedule->getOriginal(),
        'changes' => $schedule->getChanges(),
      ]);
    });

    static::deleted(function (DriverSchedule $schedule) {
      $driver = $schedule->driver;
      $date = $schedule->starts_at->toDateString();

      Log::info("Driver $driver->first_name $driver->last_name deleted $date", [
        'id' => $schedule->id,
        'driver_id' => $schedule->driver_id,
        'starts_at' => $schedule->starts_at?->toDateTimeString(),
        'ends_at' => $schedule->ends_at?->toDateTimeString(),
        'created_at' => $schedule->created_at?->toDateTimeString(),
        'updated_at' => $schedule->updated_at?->toDateTimeString(),
      ]);
    });
  }

  public function hoursInRange(Carbon $rangeStart, Carbon $rangeEnd): float
  {
    $start = max($this->starts_at->timestamp, $rangeStart->timestamp);
    $end = min($this->ends_at->timestamp, $rangeEnd->timestamp);

    return max(0, ($end - $start) / 3600);
  }

  public function driver(): BelongsTo
  {
    return $this->belongsTo(Driver::class);
  }

  public function setEndsAtAttribute($value)
  {
      $startsAt = isset($this->attributes['starts_at'])
          ? Carbon::parse($this->attributes['starts_at'])
          : Carbon::now();

      $endsTime = Carbon::parse($value)->format('H:i:s');
      $endsAt = Carbon::parse($startsAt->toDateString() . ' ' . $endsTime);

      if ($endsAt->lessThanOrEqualTo($startsAt)) {
          $endsAt->addDay();
      }

      $this->attributes['ends_at'] = $endsAt;
  }

}
