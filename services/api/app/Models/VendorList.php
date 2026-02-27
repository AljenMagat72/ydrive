<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $vendor_id
 * @property string|null $no_opps_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $vendor_name
 * @property string|null $no_opps_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Driver> $drivers
 * @property-read int|null $drivers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereNoOppsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereNoOppsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereVendorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorList whereVendorOrNoOpps($name)
 * @mixin \Eloquent
 */
class VendorList extends Model
{
  use HasFactory;

  protected $table = 'vendor_lists';

  protected $fillable = [
    'vendor_id',
    'no_opps_id',
  ];

  public function drivers()
  {
    return $this->hasMany(Driver::class, 'vendor_list_id');
  }

  public function hasDrivers(): bool
  {
    return $this->drivers()->exists();
  }
  public function scopeWhereVendorOrNoOpps($query, $name)
  {
    return $query->where('vendor_id', $name)
      ->orWhere('no_opps_id', $name);
  }
}
