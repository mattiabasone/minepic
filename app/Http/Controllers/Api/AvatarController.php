<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvatarController extends BaseApiController
{
    /**
     * Serve Avatar.
     *
     * @param \Illuminate\Http\Request
     * @param string $uuid
     * @param int    $size
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Response
     */
    public function serveUuid(Request $request, string $uuid, $size = 0): Response
    {
        $this->uuidResolver->resolve($uuid);
        $this->dispatchAccountImageServedEvent();

        return $this->pngResponse(
            (string) $this->rendering->avatar(
                $this->uuidResolver->getUuid(),
                (int) $size
            )
        );
    }

    /**
     * @param int $size
     *
     * @throws \App\Image\Exceptions\ImageCreateFromPngFailedException
     * @throws \App\Image\Exceptions\ImageTrueColorCreationFailedException
     * @throws \App\Image\Exceptions\InvalidSectionSpecifiedException
     *
     * @return Response
     */
    public function serveDefault($size = 0): Response
    {
        return $this->pngResponse(
            (string) $this->rendering->avatar(
                null,
                (int) $size
            )
        );
    }
}
