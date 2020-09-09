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
        $this->uuidResolver->resolve($uuid);
        $this->dispatchAccountImageServedEvent();

        return $this->pngResponse(
            (string) $this->rendering->isometricAvatar($this->uuidResolver->getUuid(), (int) $size)
        );
    }

    /**
     * @param int $size
     *
     * @throws \Minepic\Image\Exceptions\SkinNotFountException
     * @throws \Throwable
     *
     * @return Response
     */
    public function serveDefault($size = 0): Response
    {
        return $this->pngResponse(
            (string) $this->rendering->isometricAvatar(null, (int) $size)
        );
    }
}
