<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Notification;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::clear();

        Notification::fake();

        Http::preventStrayRequests();

        Http::fake([
            '*/v1/login/refresh' => Http::response([
                'refreshToken' => Str::uuid()->toString(),
                'token' => Str::uuid()->toString(),
            ], 200),
            '*/v1/clients/*' => Http::response([
                'id' => Str::uuid()->toString(),
                'phoneNumber' => '+1234567890',
            ]),
        ]);
    }
}
