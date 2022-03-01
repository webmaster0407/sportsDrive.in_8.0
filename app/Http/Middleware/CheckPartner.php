<?php

namespace App\Http\Middleware;

use Closure;

class CheckPartner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $partner = session('partner');
        if(!$partner)
            return redirect("partner/login");
        return $next($request);
    }
}
