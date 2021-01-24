<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SkinFrontController extends BaseApiController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $uuid User UUID or Username
     * @param int    $size
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Response
     */
    public function serveUuid(Request $request, string $uuid, $size = 0): Response
    {
        $size = (int) $size;
        $this->uuidResolver->resolve($uuid);
        $this->dispatchAccountImageServedEvent();

        return $this->pngResponse(
            (string) $this->rendering->skinFront($this->uuidResolver->getUuid(), $size)
        );
    }

    /**
     * @param int|string $size
     *
     * @throws \Throwable
     *
     * @return Response
     */
    public function serveDefault($size = 0): Response
    {
        $image = $this->cache()->remember('rendering.system.default_skin_front', 3600, function () use ($size) {
            return (string) $this->rendering->skinFront(
                null,
                (int) $size
            );
        });

        return $this->pngResponse($image);
    }
}
