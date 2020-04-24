<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core as MinepicCore;
use App\Resolvers\UsernameResolver;
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
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var UsernameResolver
     */
    protected UsernameResolver $usernameResolver;

    /**
     * Api constructor.
     *
     * @param MinepicCore      $minepic          Minepic Core Instance
     * @param ResponseFactory  $responseFactory  Response Factory
     * @param UsernameResolver $usernameResolver
     */
    public function __construct(
        MinepicCore $minepic,
        ResponseFactory $responseFactory,
        UsernameResolver $usernameResolver
    ) {
        $this->minepic = $minepic;
        $this->responseFactory = $responseFactory;
        $this->usernameResolver = $usernameResolver;
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
     * @param Request $request
     * @param string  $username
     * @param int     $size
     *
     * @throws \Throwable
     *
     * @return Response
     */
    public function serveUsername(Request $request, string $username, $size = 0): Response
    {
        $uuid = $this->usernameResolver->resolve($username);

        return $this->serveUuid($request, $uuid, $size);
    }

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
