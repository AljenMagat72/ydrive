<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientNotification extends Model
{
  protected $fillable = [
    'ride_id',
    'client_id',
    'driver_id',
    'notification_type',
  ];
}
