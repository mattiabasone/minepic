<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Image\ImageSection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class BaseApiController.
 */
class SkinBackController extends BaseApiController
{
    /**
     * Serve Avatar.
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
        $this->minepic->initialize($uuid);
        $this->minepic->updateStats();

        return $this->pngResponse(
            (string) $this->minepic->renderSkinCurrentUser($size, ImageSection::BACK)
        );
    }

    /**
     * @param Request $request
     * @param string  $username Username
     * @param int     $size
     *
     * @throws \Throwable
     *
     * @return Response
     */
    public function serveUsername(Request $request, $username, $size = 0): Response
    {
        $size = (int) $size;
        $this->minepic->initialize($username);
        $this->minepic->updateStats();

        return $this->pngResponse(
            (string) $this->minepic->renderSkinCurrentUser($size, ImageSection::BACK)
        );
    }
}
