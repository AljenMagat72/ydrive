<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
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
 * @property string $uuid
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DriverSchedule whereUuid($value)
 * @mixin \Eloquent
 */
class DriverSchedule extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'starts_at',
        'ends_at',
        'uuid',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->logExcept(['updated_at', 'created_at']);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
