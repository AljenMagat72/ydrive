<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\Driver\DriverResource;
use App\Models\Driver;

class DriverController extends Controller
{
    public function get(Driver $driver)
    {
        return new DriverResource($driver);
    }
}
