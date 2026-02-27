<?php

namespace App\Models;

use App\Notifications\LoginCodeNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
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
class SMSCode extends Model
{
  protected $table = 'sms_codes';

  protected $fillable = [
    'driver_id',
  ];

  protected static function booted(): void
  {
    static::creating(function (SMSCode $model) {
      $model->code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    });

    static::created(function (SMSCode $smsCode) {
      $smsCode->driver->notify(new LoginCodeNotification($smsCode->code));
    });
  }

  public function driver(): BelongsTo
  {
    return $this->belongsTo(Driver::class);
  }
}
