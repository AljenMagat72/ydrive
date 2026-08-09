<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Cities
{
    public static function toOptions(): array
    {
        return collect(config('autofleet.cities'))
            ->mapWithKeys(fn($data, $key) => [
                Str::headline($key) => Str::headline($key),
            ])
            ->toArray();
    }

    public static function toArray(): array
    {
        return collect(config('autofleet.cities'))
            ->keys()
            ->map(fn($key) => Str::headline($key))
            ->toArray();
    }

    public static function all(): array
    {
        return collect(config('autofleet.cities'))->toArray();
    }
}
