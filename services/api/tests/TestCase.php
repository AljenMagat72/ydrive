<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Notification;

abstract class TestCase extends BaseTestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Notification::fake();

    Http::fake([
      'https://api.autofleet.io/api/v1/login/refresh' => Http::response([
        'refreshToken' => Str::uuid()->toString(),
        'token' => Str::uuid()->toString(),
      ], 200),
      'https://api.autofleet.io/api/v1/clients/*' => Http::response([
        'id' => Str::uuid()->toString(),
        'phoneNumber' => '+1234567890',
      ]),
      '*' => Http::response([], 200),
    ]);
  }
}
