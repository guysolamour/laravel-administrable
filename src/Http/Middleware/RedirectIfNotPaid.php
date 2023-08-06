<?php

namespace Guysolamour\Administrable\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectIfNotPaid
{
    private const DEFAULT_ROUTE_NAME = 'front.notpaid';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (option_get('app_paid') && !$this->isNotPaidRoute($request)) {
            return redirect('/');
        }

        if (!option_get('app_paid') && $this->isNotPaidRoute($request)){
            return redirect()->route(self::DEFAULT_ROUTE_NAME);
        }

        return $next($request);
    }

    private function isNotPaidRoute($request) :bool
    {
        return self::DEFAULT_ROUTE_NAME != Route::getRoutes()->match($request)->getName();
    }
}
