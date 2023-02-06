<?php

declare(strict_types=1);

namespace Minepic\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Add cache related HTTP headers.
     *
     * @throws \InvalidArgumentException
     */
    public function handle(Request $request, \Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        if (!$request->isMethodCacheable() || !$response->getContent()) {
            return $response;
        }

        $response->setEtag(md5($response->getContent()), false);
        $response->setPublic();
        $response->setMaxAge((int) env('USERDATA_CACHE_TIME'));
        $response->isNotModified($request);

        return $response;
    }
}
