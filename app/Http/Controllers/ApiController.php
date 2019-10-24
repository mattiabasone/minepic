<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class ApiController.
 */
class ApiController extends BaseController
{
    /**
     * @var MinepicCore
     */
    private $minepic;

    /** @var ResponseFactory */
    private $responseFactory;

    /**
     * Api constructor.
     *
     * @param MinepicCore     $minepic
     * @param ResponseFactory $responseFactory
     */
    public function __construct(MinepicCore $minepic, ResponseFactory $responseFactory)
    {
        $this->minepic = $minepic;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Serve Avatar.
     *
     * @param \Illuminate\Http\Request
     * @param string $uuidOrName
     * @param int    $size
     *
     * @return Response
     */
    public function serveAvatar(Request $request, $uuidOrName = '', $size = 0): Response
    {
        $size = (int) $size;

        $this->minepic->initialize($uuidOrName);
        $headers = $this->minepic->generateHttpCacheHeaders($size, 'avatar');
        $this->minepic->updateStats();

        if ($request->server('HTTP_IF_MODIFIED_SINCE')) {
            $avatarImage = '';
            $httpCode = 304;
        } else {
            $avatarImage = $this->minepic->avatarCurrentUser($size);
            $httpCode = 200;
            $headers['Content-Type'] = 'image/png';
        }

        return $this->responseFactory->make($avatarImage, $httpCode, $headers);
    }

    /**
     * Serve avatar with size.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $size
     * @param string                   $uuidOrName
     *
     * @return Response
     */
    public function avatarWithSize(Request $request, $size = 0, string $uuidOrName = ''): Response
    {
        return $this->serveAvatar($request, $uuidOrName, $size);
    }

    /**
     * Serve isometric avatar.
     *
     * @param \Illuminate\Http\Request
     * @param string $uuidOrName
     * @param int    $size
     *
     * @return Response
     */
    public function serveIsometricAvatar(Request $request, $uuidOrName = '', $size = 0): Response
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

    /**
     * Isometric Avatar with Size.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $size
     * @param string                   $uuidOrName
     *
     * @return Response
     */
    public function isometricAvatarWithSize(Request $request, $size = 0, $uuidOrName = ''): Response
    {
        return $this->serveIsometricAvatar($request, $uuidOrName, $size);
    }

    /**
     * Serve skin.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $uuidOrName
     * @param int                      $size
     * @param string                   $type
     *
     * @return Response
     *
     * @throws \Throwable
     */
    public function serveSkin(Request $request, $uuidOrName = '', $size = 0, $type = 'F'): Response
    {
        $size = (int) $size;
        $this->minepic->initialize($uuidOrName);
        $headers = $this->minepic->generateHttpCacheHeaders($size, 'avatar');
        $this->minepic->updateStats();

        if ($request->server('HTTP_IF_MODIFIED_SINCE')) {
            $avatarImage = '';
            $httpCode = 304;
        } else {
            $avatarImage = $this->minepic->renderSkinCurrentUser($size, $type);
            $httpCode = 200;
            $headers['Content-Type'] = 'image/png';
        }

        return $this->responseFactory->make($avatarImage, $httpCode, $headers);
    }

    /**
     * Skin front with size parameter.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $uuidOrName
     * @param $size
     *
     * @return Response
     *
     * @throws \Throwable
     */
    public function skinFrontWithSize(Request $request, $uuidOrName, $size): Response
    {
        return $this->serveSkin($request, $uuidOrName, $size);
    }

    /**
     * Skin back without size parameter.
     *
     * @param \Illuminate\Http\Request $request
     * @param $uuidOrName
     *
     * @return Response
     *
     * @throws \Throwable
     */
    public function skinBackWithoutSize(Request $request, $uuidOrName): Response
    {
        return $this->serveSkin($request, $uuidOrName, 0, 'B');
    }

    /**
     * Skin back with size parameter.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $uuidOrName
     * @param $size
     *
     * @return Response
     *
     * @throws \Throwable
     */
    public function skinBackWithSize(Request $request, $uuidOrName, $size): Response
    {
        return $this->serveSkin($request, $uuidOrName, $size, 'B');
    }

    /**
     * Download the skin.
     *
     * @param string $uuidOrName
     *
     * @return Response
     */
    public function downloadTexture(string $uuidOrName = ''): Response
    {
        $headers = [
            'Content-Disposition' => 'Attachment;filename='.$uuidOrName.'.png',
            'Content-Type' => 'image/png',
        ];
        $this->minepic->initialize($uuidOrName);
        $avatarImage = $this->minepic->skinCurrentUser();
        $avatarImage->prepareTextureDownload();

        return $this->responseFactory->make($avatarImage, 200, $headers);
    }

    /**
     * Update User data.
     *
     * @param string $uuidOrName
     *
     * @return JsonResponse
     */
    public function update(string $uuidOrName): JsonResponse
    {
        // Force user update
        $this->minepic->setForceUpdate(true);

        // Check if user exists
        if ($this->minepic->initialize($uuidOrName)) {
            // Check if data has been updated
            if ($this->minepic->userDataUpdated()) {
                $response = ['ok' => true, 'message' => 'Data updated'];
                $httpStatus = 200;
            } else {
                $userdata = $this->minepic->getUserdata();
                $dateString = $userdata->updated_at->toW3cString();

                $response = [
                    'ok' => false,
                    'message' => 'Cannot update user, userdata has been updated recently',
                    'last_update' => $dateString,
                ];

                $httpStatus = 403;
            }
        } else {
            $response = ['ok' => false, 'message' => 'User not found'];
            $httpStatus = 404;
        }

        return $this->responseFactory->json($response, $httpStatus);
    }
}
