<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
