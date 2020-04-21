<?php

namespace App\Http\Middleware;

use Closure;

class SetCacheHeaders
{
    /**
     * Add cache related HTTP headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     */
    public function handle($request, Closure $next)
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        if (! $request->isMethodCacheable() || !$response->getContent()) {
            return $response;
        }

        $response->setEtag(md5($response->getContent()));
        $response->setPublic();
        $response->setMaxAge(env('USERDATA_CACHE_TIME'));
        $response->isNotModified($request);

        return $response;
    }
}
