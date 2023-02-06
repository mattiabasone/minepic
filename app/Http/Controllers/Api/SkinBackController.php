<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SkinBackController extends BaseApiController
{
    /**
     * Serve Avatar.
     *
     * @param string $uuid User UUID
     * @param int    $size
     *
     * @throws \Throwable
     */
    public function serveUuid(Request $request, $uuid, $size = 0): Response
    {
        $this->uuidResolver->resolve($uuid);
        $this->dispatchAccountImageServedEvent();

        return $this->pngResponse(
            (string) $this->rendering->skinBack($this->uuidResolver->getUuid(), (int) $size)
        );
    }

    /**
     * @param int|string $size
     *
     * @throws \Throwable
     */
    public function serveDefault($size = 0): Response
    {
        $image = $this->cache()->remember('rendering.system.default_skin_back', 3600, function () use ($size) {
            return (string) $this->rendering->skinBack(
                null,
                (int) $size
            );
        });

        return $this->pngResponse($image);
    }
}
