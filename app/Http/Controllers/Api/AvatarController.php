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
     * @param string $uuidOrName
     * @param int    $size
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Response
     */
    public function serve(Request $request, $uuidOrName = '', $size = 0): Response
    {
        $size = (int) $size;

        $this->minepic->initialize($uuidOrName);
        $this->minepic->updateStats();

        return $this->pngResponse(
            (string) $this->minepic->avatarCurrentUser($size)
        );
    }
}
