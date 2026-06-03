<?php

namespace App\Services\Support;

class TokenService
{
    public function generate(int $length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public function hash(string $token)
    {
        return hash('sha256', $token);
    }
}
