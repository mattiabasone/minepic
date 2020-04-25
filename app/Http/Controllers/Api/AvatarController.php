<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class BaseApiController.
 */
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
        $size = (int) $size;

        $this->minepic->initialize($uuid);
        $this->minepic->updateStats();

        $skinPath = $this->minepic->getCurrentUserSkinImage();

        return $this->pngResponse(
            (string) $this->rendering->avatar($skinPath, $size)
        );
    }
}
