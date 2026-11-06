<?php

namespace App\Http\Middleware;

use Closure;

class EnsureWmsSession
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('wms.token') || !session()->has('wms.user')) {
            return redirect()->away(config('wms.trans_login_url'));
        }

        return $next($request);
    }
}
