<?php

use App\Http\Middleware\HandleDriverInertiaRequests;

Route::mount('driver', [HandleDriverInertiaRequests::class]);
