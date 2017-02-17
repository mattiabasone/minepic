<?php

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class Api extends BaseController
{
    /**
     * @var MinepicCore
     */
    private $minepic;

    /**
     * Api constructor.
     */
    public function __construct() {
        $this->minepic = new MinepicCore();
    }

    /**
     * Serve Avatar
     *
     * @param string $uuidOrName
     * @param int $size
     * @return Response
     */
    public function serveAvatar($uuidOrName = '', $size = 0) : Response {
        $size = (int) $size;

        $this->minepic->initialize($uuidOrName);
        $headers = $this->minepic->generateHttpCacheHeaders($size, 'avatar');
        $this->minepic->updateStats();


        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $avatarImage = '';
            $httpCode = 304;
        } else {
            $avatarImage = $this->minepic->avatarCurrentUser($size);
            $httpCode = 200;
            $headers['Content-Type'] = 'image/png';
        }

        return Response::create($avatarImage, $httpCode, $headers);
    }

    /**
     * Serve avatar with size
     *
     * @param int $size
     * @param string $uuidOrName
     * @return Response
     */
    public function avatarWithSize($size = 0, $uuidOrName = '') : Response {
        return $this->serveAvatar($uuidOrName, $size);
    }

    /**
     * Serve skin
     *
     * @param string $uuidOrName
     * @param int $size
     * @param string $type
     * @return Response
     */
    public function serveSkin($uuidOrName = '', $size = 0, $type = 'F') : Response {
        $size = (int) $size;
        $this->minepic->initialize($uuidOrName);
        $headers = $this->minepic->generateHttpCacheHeaders($size, 'avatar');
        $this->minepic->updateStats();

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $avatarImage = '';
            $httpCode = 304;
        } else {
            $avatarImage = $this->minepic->renderSkinCurrentUser($size, $type);
            $httpCode = 200;
            $headers['Content-Type'] = 'image/png';
        }

        return Response::create($avatarImage, $httpCode, $headers);
    }

    /**
     * Skin front with size parameter
     *
     * @param $uuidOrName
     * @param $size
     * @return Response
     */
    public function skinFrontWithSize($uuidOrName, $size) : Response {
        return $this->serveSkin($uuidOrName, $size);
    }

    /**
     * Skin back without size parameter
     *
     * @param $uuidOrName
     * @return Response
     */
    public function skinBackWithoutSize($uuidOrName) : Response {
        return $this->serveSkin($uuidOrName, 0, 'B');
    }

    /**
     * Skin back with size parameter
     *
     * @param $uuidOrName
     * @param $size
     * @return Response
     */
    public function skinBackWithSize($uuidOrName, $size) : Response {
        return $this->serveSkin($uuidOrName, $size, 'B');
    }

    /**
     * Download the skin
     *
     * @param string $uuidOrName
     * @return Response
     */
    public function downloadTexture(string $uuidOrName = '') : Response {
        $headers = [
            'Content-Disposition' => 'Attachment;filename='.$uuidOrName.'.png',
            'Content-Type' => 'image/png'
        ];
        $this->minepic->initialize($uuidOrName);
        $avatarImage = $this->minepic->skinCurrentUser();
        $avatarImage->prepareTextureDownload();
        return Response::create($avatarImage, 200, $headers);
    }

    /**
     * Update userdata
     *
     * @param string $uuidOrName
     * @return Response
     */
    public function update(string $uuidOrName) : Response {
        if ($this->minepic->initialize($uuidOrName)) {
            if ($this->minepic->forceUserUpdate()) {
                $response = ['ok' => true, 'message' => 'Data updated'];
                $httpStatus = 200;
            } else {
                $response = ['ok' => false, 'message' => 'Cannot update user information, try again later'];
                $httpStatus = 403;
            }
        } else {
            $response = ['ok' => false, 'message' => 'User not found'];
            $httpStatus = 404;
        }
        return Response::create($response, $httpStatus, ['Content-Type' =>'application-json']);
    }
}