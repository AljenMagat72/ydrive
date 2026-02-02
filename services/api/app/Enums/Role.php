<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case DRIVER = 'driver';
    case DISPATCHER = 'dispatcher';
    case AUDITOR = 'auditor';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
