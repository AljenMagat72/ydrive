<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Settings\DriverPortalSettings;
use Illuminate\Http\Request;

class DriverSettingsController extends Controller
{
    public function __construct(
        protected DriverPortalSettings $settings
    ) {}

    public function index()
    {
        return response()->json($this->settings->toArray());
    }

    public function show(string $key)
    {
        if (!array_key_exists($key, $this->settings->toArray())) {
            abort(404);
        }

        return response()->json($this->settings->toArray()[$key]);
    }
}
