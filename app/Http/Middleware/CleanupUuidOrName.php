<?php

declare(strict_types=1);

namespace Minepic\Http\Middleware;

use Illuminate\Http\Request;

class CleanupUuidOrName
{
    public function handle(Request $request, \Closure $next): mixed
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

    private function cleanUuid(string $uuid): string
    {
        $uuid = (string) preg_replace("#\.png.*#", '', $uuid);

        return str_replace('-', '', $uuid);
    }

    private function cleanUsername(string $username): string
    {
        return preg_replace("#\.png$#", '', $username) ?? '';
    }
}
