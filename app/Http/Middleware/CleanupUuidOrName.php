<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CleanupUuidOrName
{
    public function handle(Request $request, Closure $next)
    {
        /** @var array $route */
        $route = $request->route();
        if (isset($route[2]['uuidOrName'])) {
            $route[2]['uuidOrName'] = \preg_replace("#\.png.*#", '', $route[2]['uuidOrName']);
            $route[2]['uuidOrName'] = \preg_replace('#[^a-zA-Z0-9_]#', '', $route[2]['uuidOrName']);
            $request->setRouteResolver(static function () use ($route) {
                return $route;
            });
        }

        return $next($request);
    }
}
