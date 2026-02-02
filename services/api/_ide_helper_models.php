<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $client_id
 * @property string $ride_id
 * @property string|null $driver_id
 * @property string $notification_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification whereRideId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientNotification whereUpdatedAt($value)
 */
	class ClientNotification extends \Eloquent {}
}

namespace App\Models{
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
 * @property float|null $weekly_acceptance_rate
 * @property float|null $minimum_scheduled_hours
 * @property float|null $minimum_acceptance_rate
 * @property int|null $vendor_list_id
 * @property-read \App\Models\DriverDelinquentPeriod|null $currentDelinquentPeriod
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereMinimumAcceptanceRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereMinimumScheduledHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereVendorListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereWeeklyAcceptanceRate($value)
 */
	class Driver extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Driver|null $driver
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $driver_id
 * @property string $started_at
 * @property string|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverDelinquentPeriod whereUpdatedAt($value)
 */
	class DriverDelinquentPeriod extends \Eloquent {}
}

namespace App\Models{
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
	class DriverSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $token
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrationToken whereUpdatedAt($value)
 */
	class RegistrationToken extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $driver_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver $driver
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SMSCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class SMSCode extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Enums\Role $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

