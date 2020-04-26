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
        if (isset($route[2]['uuid'])) {
            $route[2]['uuid'] = $this->cleanUuid($route[2]['uuid']);
        }

        if (isset($route[2]['username'])) {
            $route[2]['username'] = $this->cleanUsername($route[2]['username']);
        }

        $request->setRouteResolver(static function () use ($route) {
            return $route;
        });

        return $next($request);
    }

    /**
     * @param string $uuid
     *
     * @return string|string[]
     */
    private function cleanUuid(string $uuid)
    {
        $uuid = \preg_replace("#\.png.*#", '', $uuid);

        return \str_replace('-', '', $uuid);
    }

    /**
     * @param string $username
     *
     * @return string|string[]|null
     */
    private function cleanUsername(string $username)
    {
        return \preg_replace("#\.png$#", '', $username);
    }
}
