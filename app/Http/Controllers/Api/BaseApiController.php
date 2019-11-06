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
     */
    public function __construct(
        MinepicCore $minepic,
        ResponseFactory $responseFactory
    ) {
        $this->minepic = $minepic;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $uuidOrName
     * @param int    $size
     */
    abstract public function serve(Request $request, $uuidOrName = '', $size = 0): Response;

    /**
     * Isometric Avatar with Size.
     *
     * @param int    $size
     * @param string $uuidOrName
     */
    public function serveWithSize(Request $request, $size = 0, $uuidOrName = ''): Response
    {
        return $this->serve($request, $uuidOrName, $size);
    }
}
