<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

/**
 * 
 *
 * @property int $id
 * @property string $autofleet_driver_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $avatar
 * @property string|null $city_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $phone_number
 * @property int $is_delinquent
 * @property int $prevent_delinquency
 * @property int|null $minimum_schedule_hours
 * @property int $consecutive_delinquent_weeks
 * @property string|null $vendor_id
 * @property string|null $original_vendor_id
 * @property int $is_active
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DriverDelinquentPeriod> $deliquentPeriods
 * @property-read int|null $deliquent_periods_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DriverSchedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static Builder<static>|Driver newModelQuery()
 * @method static Builder<static>|Driver newQuery()
 * @method static Builder<static>|Driver query()
 * @method static Builder<static>|Driver underHoursForRange(\Illuminate\Support\Carbon $rangeStart, \Illuminate\Support\Carbon $rangeEnd)
 * @method static Builder<static>|Driver whereAutofleetDriverId($value)
 * @method static Builder<static>|Driver whereAvatar($value)
 * @method static Builder<static>|Driver whereCityId($value)
 * @method static Builder<static>|Driver whereConsecutiveDelinquentWeeks($value)
 * @method static Builder<static>|Driver whereCreatedAt($value)
 * @method static Builder<static>|Driver whereFirstName($value)
 * @method static Builder<static>|Driver whereId($value)
 * @method static Builder<static>|Driver whereIsActive($value)
 * @method static Builder<static>|Driver whereIsDelinquent($value)
 * @method static Builder<static>|Driver whereLastName($value)
 * @method static Builder<static>|Driver whereOriginalVendorId($value)
 * @method static Builder<static>|Driver wherePhoneNumber($value)
 * @method static Builder<static>|Driver wherePreventDelinquency($value)
 * @method static Builder<static>|Driver whereUpdatedAt($value)
 * @method static Builder<static>|Driver whereVendorId($value)
 * @mixin \Eloquent
 */
class Driver extends User
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * @return \Illuminate\Support\Collection<int, object{driver: \App\Models\Driver, totalHours: float}>
   */
  public static function InCompleteSchedules(): Collection
  {
    $hours = 20;

    $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();
    $nextWeekEnd = (clone $nextWeekStart)->endOfWeek();

    return Driver::underHoursForRange($nextWeekStart, $nextWeekEnd)
      ->get()
      ->map(function ($driver) use ($hours, $nextWeekStart, $nextWeekEnd) {
        $total = $driver->schedules->sum(
          fn($s) => $s->hoursInRange($nextWeekStart, $nextWeekEnd)
        );

        return (object) [
          'driver' => $driver,
          'totalHours' => $total,
        ];
      })
      ->filter(fn($item) => $item->totalHours < $hours);
  }

  protected $fillable = [
    'autofleet_driver_id',
    'avatar',
    'first_name',
    'last_name',
    'city_id',
    'phone_number',
    'is_delinquent',
    'prevent_delinquency',
    'is_active',
    'original_vendor_id',
    'acceptance_rate',
    'minimum_scheduled_hours',
    'acceptance_rate_needed',
    'rejected_offers',
    'expired_offers',
  ];

  public function scopeUnderHoursForRange(
    Builder $query,
    Carbon $rangeStart,
    Carbon $rangeEnd
  ) {
    return $query
      ->where('prevent_delinquency', false)
      ->where('is_active', true)
      ->with([
        'schedules' => function ($q) use ($rangeStart, $rangeEnd) {
          $q->whereBetween('starts_at', [$rangeStart, $rangeEnd])
            ->orWhereBetween('ends_at', [$rangeStart, $rangeEnd]);
        }
      ]);
  }

  public function schedules()
  {
    return $this->hasMany(DriverSchedule::class);
  }

  public function deliquentPeriods()
  {
    return $this->hasMany(DriverDelinquentPeriod::class);
  }

  public function currentDelinquentPeriod()
  {
    return $this->hasOne(DriverDelinquentPeriod::class)
      ->whereNull('ended_at');
  }
}
