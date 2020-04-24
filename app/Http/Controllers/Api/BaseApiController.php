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
abstract class BaseApiController extends BaseController
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
     * @param MinepicCore     $minepic         Minepic Core Instance
     * @param ResponseFactory $responseFactory Response Factory
     */
    public function __construct(
        MinepicCore $minepic,
        ResponseFactory $responseFactory
    ) {
        $this->minepic = $minepic;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param Request $request Injected Request
     * @param string  $uuid
     * @param int     $size    Avatar size User UUID or name
     *
     * @return Response
     */
    abstract public function serveUuid(Request $request, string $uuid, $size = 256): Response;

    /**
     * @param Request $request  Injected Request
     * @param string  $username
     * @param int     $size     Avatar size User UUID or name
     *
     * @return Response
     */
    abstract public function serveUsername(Request $request, string $username, $size = 256): Response;

    /**
     * @param string $image
     *
     * @return Response
     */
    public function pngResponse(string $image)
    {
        return $this->responseFactory->make($image, Response::HTTP_OK, ['Content-Type' => 'image/png']);
    }
}
