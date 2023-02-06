<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsometricAvatarController extends BaseApiController
{
    /**
     * Serve isometric avatar.
     *
     * @param string $uuid User UUID
     * @param int    $size
     *
     * @throws \Throwable
     */
    public function serveUuid(Request $request, string $uuid, $size = 0): Response
    {
        $this->uuidResolver->resolve($uuid);
        $this->dispatchAccountImageServedEvent();

        return $this->pngResponse(
            (string) $this->rendering->isometricAvatar($this->uuidResolver->getUuid(), (int) $size)
        );
    }

    /**
     * @param int|string $size
     *
     * @throws \Throwable
     */
    public function serveDefault($size = 0): Response
    {
        $image = $this->cache()->remember('rendering.system.default_isometric_avatar', 3600, function () use ($size) {
            return (string) $this->rendering->isometricAvatar(null, (int) $size);
        });

        return $this->pngResponse($image);
    }
}
