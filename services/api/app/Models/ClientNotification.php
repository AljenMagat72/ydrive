<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
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
 * @mixin \Eloquent
 */
class ClientNotification extends Model
{
  protected $fillable = [
    'ride_id',
    'client_id',
    'driver_id',
    'notification_type',
  ];
}
