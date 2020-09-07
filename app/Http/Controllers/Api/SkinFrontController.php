<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Image\ImageSection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
}
