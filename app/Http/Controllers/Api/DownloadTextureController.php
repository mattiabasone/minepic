<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core as MinepicCore;
use App\Image\Sections\Skin;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class BaseApiController.
 */
class DownloadTextureController extends BaseController
{
    /**
     * @var MinepicCore
     */
    protected MinepicCore $minepic;

    /** @var ResponseFactory */
    protected ResponseFactory $responseFactory;

    /**
     * Api constructor.
     *
     * @param MinepicCore     $minepic
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        MinepicCore $minepic,
        ResponseFactory $responseFactory
    ) {
        $this->minepic = $minepic;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Download user texture.
     *
     * @param string $uuid User UUID or Username
     *
     * @throws \App\Image\Exceptions\ImageCreateFromPngFailedException
     *
     * @return \Illuminate\Http\Response
     */
    public function serve(string $uuid = ''): Response
    {
        $headers = [
            'Content-Disposition' => 'Attachment;filename='.$uuid.'.png',
            'Content-Type' => 'image/png',
        ];
        $this->minepic->initialize($uuid);
        $userSkin = new Skin($this->minepic->getCurrentUserSkinImage());
        $userSkin->prepareTextureDownload();

        return $this->responseFactory->make($userSkin, 200, $headers);
    }
}
