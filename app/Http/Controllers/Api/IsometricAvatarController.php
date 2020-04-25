<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsometricAvatarController extends BaseApiController
{
    /**
     * Serve isometric avatar.
     *
     * @param \Illuminate\Http\Request
     * @param string $uuid User UUID
     * @param int    $size
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Response
     */
    public function serveUuid(Request $request, $uuid, $size = 0): Response
    {
        $size = (int) $size;

        $this->uuidResolver->resolve($uuid);
        $this->uuidResolver->updateStats();

        return $this->pngResponse(
            (string) $this->rendering->isometricAvatar($this->uuidResolver->getUuid(), $size)
        );
    }
}
