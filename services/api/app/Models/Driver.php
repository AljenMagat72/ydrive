<?php

namespace App\Models;

use App\Enums\UserType;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $autofleet_driver_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $avatar
 * @property string $city_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $phone_number
 * @property bool $is_delinquent
 * @property bool $is_active
 * @property float|null $weekly_acceptance_rate
 * @property numeric|null $minimum_scheduled_hours
 * @property float|null $minimum_acceptance_rate
 * @property int|null $vendor_list_id
 * @property int|null $acceptance_rate
 * @property int $acceptance_rate_needed
 * @property int $rejected_offers
 * @property int $expired_offers
 * @property string|null $zoho_id
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DriverSchedule> $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\VendorList|null $vendor
 * @method static Builder<static>|Driver belowAcceptanceRate()
 * @method static \Database\Factories\DriverFactory factory($count = null, $state = [])
 * @method static Builder<static>|Driver newModelQuery()
 * @method static Builder<static>|Driver newQuery()
 * @method static Builder<static>|Driver query()
 * @method static Builder<static>|Driver underHoursForRange(\Illuminate\Support\Carbon $rangeStart, \Illuminate\Support\Carbon $rangeEnd)
 * @method static Builder<static>|Driver whereAcceptanceRate($value)
 * @method static Builder<static>|Driver whereAcceptanceRateNeeded($value)
 * @method static Builder<static>|Driver whereAutofleetDriverId($value)
 * @method static Builder<static>|Driver whereAvatar($value)
 * @method static Builder<static>|Driver whereCityId($value)
 * @method static Builder<static>|Driver whereCreatedAt($value)
 * @method static Builder<static>|Driver whereExpiredOffers($value)
 * @method static Builder<static>|Driver whereFirstName($value)
 * @method static Builder<static>|Driver whereId($value)
 * @method static Builder<static>|Driver whereIsActive($value)
 * @method static Builder<static>|Driver whereIsDelinquent($value)
 * @method static Builder<static>|Driver whereLastName($value)
 * @method static Builder<static>|Driver whereMinimumAcceptanceRate($value)
 * @method static Builder<static>|Driver whereMinimumScheduledHours($value)
 * @method static Builder<static>|Driver wherePhoneNumber($value)
 * @method static Builder<static>|Driver whereRejectedOffers($value)
 * @method static Builder<static>|Driver whereUpdatedAt($value)
 * @method static Builder<static>|Driver whereVendorListId($value)
 * @method static Builder<static>|Driver whereWeeklyAcceptanceRate($value)
 * @method static Builder<static>|Driver whereZohoId($value)
 * @property string $uuid
 * @method static Builder<static>|Driver whereUuid($value)
 * @mixin \Eloquent
 */
class Driver extends BaseUser
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * @return \Illuminate\Support\Collection<int, object{driver: \App\Models\Driver, totalHours: float}>
   */
  public static function IncompleteSchedules(string $cityId): Collection
  {
    $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();
    $nextWeekEnd = (clone $nextWeekStart)->endOfWeek();

    return Driver::underHoursForRange($nextWeekStart, $nextWeekEnd)
      ->where('is_active', true)
      ->where('city_id', $cityId)
      ->get();
  }

  public static function NotMeetingAcceptanceRate(string $cityId): Collection
  {
    return Driver::where('is_active', true)
      ->where('city_id', $cityId)
      ->belowAcceptanceRate()
      ->get();
  }

  public function type(): UserType
  {
    return UserType::DRIVER;
  }

  protected $fillable = [
    'autofleet_driver_id',
    'avatar',
    'first_name',
    'last_name',
    'city_id',
    'phone_number',
    'is_delinquent',
    'is_active',
    'acceptance_rate',
    'minimum_scheduled_hours',
    'acceptance_rate_needed',
    'rejected_offers',
    'expired_offers',
    'zoho_id',
  ];

  public function scopeUnderHoursForRange(
    Builder $query,
    Carbon $rangeStart,
    Carbon $rangeEnd
  ) {
    $defaultHours = config('autofleet.acceptance_rate_minimum');

    return $query
      ->whereRaw('COALESCE(minimum_scheduled_hours, ?) > (
        SELECT COALESCE(SUM(EXTRACT(EPOCH FROM (ends_at - starts_at)) / 3600), 0)
        FROM driver_schedules
        WHERE driver_schedules.driver_id = drivers.id
        AND (
          (starts_at BETWEEN ? AND ?)
          OR (ends_at BETWEEN ? AND ?)
        )
      )', [
        $defaultHours,
        $rangeStart,
        $rangeEnd,
        $rangeStart,
        $rangeEnd
      ])
      ->with([
        'schedules' => function ($q) use ($rangeStart, $rangeEnd) {
          $q->whereBetween('starts_at', [$rangeStart, $rangeEnd])
            ->orWhereBetween('ends_at', [$rangeStart, $rangeEnd]);
        }
      ]);
  }

  public function scopeBelowAcceptanceRate(Builder $query)
  {
    $minAcceptanceRate = config('autofleet.acceptance_rate_minimum');
    return $query->whereRaw('COALESCE(minimum_acceptance_rate, ?) > COALESCE(weekly_acceptance_rate, 0)', [$minAcceptanceRate]);
  }

  public function routeNotificationForTwilio()
  {
    //Autofleet stores the number without a +
    return "+$this->phone_number";
  }

  public function schedules()
  {
    return $this->hasMany(DriverSchedule::class);
  }

  public function vendor()
  {
    return $this->belongsTo(VendorList::class, 'vendor_list_id');
  }
}
