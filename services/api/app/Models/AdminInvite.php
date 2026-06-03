<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
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
 * @mixin \Eloquent
 */
class AdminInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'admin_id',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        return is_null($this->accepted_at)
            && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }
}
