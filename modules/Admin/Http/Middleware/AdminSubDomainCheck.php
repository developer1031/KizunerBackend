<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Framework\Service\Facade\Environment;

class AdminSubDomainCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $urlParts = explode('.', $_SERVER['HTTP_HOST']);
        $subdomain = $urlParts[0];

        if (Environment::isProduction() && $subdomain !== 'man') {
            abort(403);
        }

        return $next($request);
    }
}
