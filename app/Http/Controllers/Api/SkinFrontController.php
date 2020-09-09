<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Minepic\Image\ImageSection;

/**
 * Class SkinFrontController.
 */
class SkinFrontController extends BaseApiController
{
    /**
     * Serve Avatar.
     *
     * @param \Illuminate\Http\Request
     * @param string $uuid User UUID or Username
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
        $this->dispatchAccountImageServedEvent();

        return $this->pngResponse(
            (string) $this->rendering->skin($this->uuidResolver->getUuid(), $size, ImageSection::FRONT)
        );
    }

    /**
     * @param int $size
     *
     * @throws \Throwable
     *
     * @return Response
     */
    public function serveDefault($size = 0): Response
    {
        return $this->pngResponse(
            (string) $this->rendering->skin(
                null,
                (int) $size,
                ImageSection::FRONT
            )
        );
    }
}
