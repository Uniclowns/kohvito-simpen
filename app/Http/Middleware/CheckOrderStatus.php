<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckOrderStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Cache::get('order_status', 'buka') === 'tutup') {
            return redirect()->route('konsumen.order-tutup');
        }

        return $next($request);
    }
}
