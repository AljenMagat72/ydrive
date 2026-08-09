<?php

namespace App\Http\Middleware;

use RuntimeException;

class Authenticate extends BaseAuthenticate
{
    protected function redirectTo($request, $guards)
    {
        if ($request->expectsJson()) {
            return null;
        }

        if (count($guards) !== 1) {
            throw new RuntimeException('Exactly one guard must be specified on auth middleware.');
        }

        $guard = $guards[0];
        $routeName = "{$guard}.login";

        if (!\Route::has($routeName)) {
            throw new RuntimeException("Login route [{$routeName}] is not defined.");
        }

        return route($routeName);
    }
}
