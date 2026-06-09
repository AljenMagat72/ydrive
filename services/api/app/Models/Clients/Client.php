<?php

namespace App\Models\Clients;

use App\Enums\UserType;
use App\Models\BaseUser;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $uuid
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $email
 * @property string|null $avatar_url
 * @property string|null $email_verified_at
 * @property string|null $device_type
 * @property string|null $last_login_at
 * @property bool $is_active
 * @property string $autofleet_client_id
 * @property string|null $zoho_rider_id
 * @property string|null $chatwoot_contact_id
 * @property-read mixed $name
 * @method static \Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereAutofleetClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereChatwootContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereZohoRiderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withoutTrashed()
 * @mixin \Eloquent
 */
#[UseFactory(ClientFactory::class)]
class Client extends BaseUser
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'phone_number',
        'is_active',
        'email',
        'email_verified_at',
        'autofleet_client_id',
        'zoho_rider_id',
        'chatwoot_contact_id',
        'avatar_url',
        'created_at',
        'device_type'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function type(): UserType
    {
        return UserType::CLIENT;
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->first_name} {$this->last_name}"
        );
    }
}
