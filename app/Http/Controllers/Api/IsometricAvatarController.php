<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class BaseApiController.
 */
class IsometricAvatarController extends BaseApiController
{
    /**
     * Serve isometric avatar.
     *
     * @param \Illuminate\Http\Request
     * @param string $uuidOrName
     * @param int    $size
     */
    public function serve(Request $request, $uuidOrName = '', $size = 0): Response
    {
        $size = (int) $size;

        $this->minepic->initialize($uuidOrName);
        $headers = $this->minepic->generateHttpCacheHeaders($size, 'avatar-isometric');
        $this->minepic->updateStats();

        if ($request->server('HTTP_IF_MODIFIED_SINCE')) {
            $avatarImage = '';
            $httpCode = 304;
        } else {
            $avatarImage = $this->minepic->isometricAvatarCurrentUser($size);
            $httpCode = 200;
            $headers['Content-Type'] = 'image/png';
        }

        return $this->responseFactory->make($avatarImage, $httpCode, $headers);
    }
}
