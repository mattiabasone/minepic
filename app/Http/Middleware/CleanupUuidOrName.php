<?php

declare(strict_types=1);

namespace Minepic\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CleanupUuidOrName
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
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
     * @return string
     */
    private function cleanUuid(string $uuid): string
    {
        $uuid = (string) preg_replace("#\.png.*#", '', $uuid);

        return str_replace('-', '', $uuid);
    }

    /**
     * @param string $username
     *
     * @return string
     */
    private function cleanUsername(string $username): string
    {
        return preg_replace("#\.png$#", '', $username) ?? '';
    }
}
