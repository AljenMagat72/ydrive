<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleAdminKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminKey = $request->header('X-Admin-Key');
        $expectedKey = config('app.admin_key');

        if (empty($expectedKey) || empty($adminKey) || !hash_equals($expectedKey, $adminKey)) {
            abort(404, 'Not found');
        }

        if ($request->header('X-Admin-Key') === config('app.admin_key')) {
            $request->attributes->set('is_admin', true);
        }

        return $next($request);
    }
}
