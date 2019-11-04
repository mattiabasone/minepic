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
     * @param Request $request
     * @param string  $uuidOrName
     * @param int     $size
     *
     * @return Response
     */
    abstract public function serve(Request $request, $uuidOrName = '', $size = 0): Response;

    /**
     * Isometric Avatar with Size.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $size
     * @param string                   $uuidOrName
     *
     * @return Response
     */
    public function serveWithSize(Request $request, $size = 0, $uuidOrName = ''): Response
    {
        return $this->serve($request, $uuidOrName, $size);
    }
}
