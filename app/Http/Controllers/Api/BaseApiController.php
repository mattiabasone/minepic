<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use Minepic\Events\Account\AccountImageServedEvent;
use Minepic\Image\Rendering;
use Minepic\Resolvers\UsernameResolver;
use Minepic\Resolvers\UuidResolver;

/**
 * Class BaseApiController.
 */
abstract class BaseApiController extends BaseController
{
    /**
     * @var UuidResolver
     */
    protected UuidResolver $uuidResolver;
    /**
     * @var ResponseFactory
     */
    protected ResponseFactory $responseFactory;
    /**
     * @var UsernameResolver
     */
    protected UsernameResolver $usernameResolver;
    /**
     * @var Rendering
     */
    protected Rendering $rendering;

    /**
     * Api constructor.
     *
     * @param UuidResolver     $uuidResolver     Minepic Core Instance
     * @param ResponseFactory  $responseFactory  Response Factory
     * @param UsernameResolver $usernameResolver
     * @param Rendering        $rendering
     */
    public function __construct(
        UuidResolver $uuidResolver,
        ResponseFactory $responseFactory,
        UsernameResolver $usernameResolver,
        Rendering $rendering
    ) {
        $this->uuidResolver = $uuidResolver;
        $this->responseFactory = $responseFactory;
        $this->usernameResolver = $usernameResolver;
        $this->rendering = $rendering;
    }

    /**
     * @param Request $request Injected Request
     * @param string  $uuid
     * @param int     $size    Avatar size User UUID or name
     *
     * @return Response
     */
    abstract public function serveUuid(Request $request, string $uuid, $size = 0): Response;

    /**
     * Serve default skin section.
     *
     * @param int $size
     *
     * @return Response
     */
    abstract public function serveDefault($size = 0): Response;

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

        return $uuid ? $this->serveUuid($request, $uuid, $size) : $this->serveDefault($size);
    }

    /**
     * @param string $image
     *
     * @return Response
     */
    public function pngResponse(string $image): Response
    {
        return $this->responseFactory->make($image, Response::HTTP_OK, ['Content-Type' => 'image/png']);
    }

    /**
     * @return void
     */
    protected function dispatchAccountImageServedEvent(): void
    {
        \Event::dispatch(new AccountImageServedEvent($this->uuidResolver->getAccount()));
    }
}
