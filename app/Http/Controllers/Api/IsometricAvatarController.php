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
     * @param string $uuidOrName User UUID or Username
     * @param int    $size
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Response
     */
    public function serveUuid(Request $request, $uuidOrName, $size = 0): Response
    {
        $size = (int) $size;

        $this->minepic->initialize($uuidOrName);
        $this->minepic->updateStats();

        return $this->pngResponse((string) $this->minepic->isometricAvatarCurrentUser($size));
    }
}
