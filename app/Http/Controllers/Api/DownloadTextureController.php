<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core as MinepicCore;
use Illuminate\Http\Request;
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
    protected $minepic;

    /** @var ResponseFactory */
    protected $responseFactory;

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
     * Serve Avatar.
     *
     * @param \Illuminate\Http\Request
     * @param string $uuidOrName
     *
     * @return Response
     *
     * @throws \Throwable
     */
    public function serve(Request $request, string $uuidOrName = ''): Response
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
}
