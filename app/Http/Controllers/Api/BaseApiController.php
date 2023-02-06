<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers\Api;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use Minepic\Events\Account\AccountImageServedEvent;
use Minepic\Image\Rendering;
use Minepic\Resolvers\UsernameResolver;
use Minepic\Resolvers\UuidResolver;

abstract class BaseApiController extends BaseController
{
    /**
     * Api constructor.
     *
     * @param UuidResolver     $uuidResolver     Minepic Core Instance
     * @param ResponseFactory  $responseFactory  Response Factory
     */
    public function __construct(
        protected UuidResolver $uuidResolver,
        protected ResponseFactory $responseFactory,
        protected UsernameResolver $usernameResolver,
        protected Rendering $rendering,
        protected Dispatcher $eventDispatcher
    ) {
    }

    /**
     * @param Request $request Injected Request
     * @param int     $size    Avatar size User UUID or name
     */
    abstract public function serveUuid(Request $request, string $uuid, $size = 0): Response;

    /**
     * Serve default skin section.
     *
     * @param int $size
     */
    abstract public function serveDefault($size = 0): Response;

    /**
     * @param int     $size
     *
     * @throws \Throwable
     */
    public function serveUsername(Request $request, string $username, $size = 0): Response
    {
        $uuid = $this->usernameResolver->resolve($username);

        return $uuid ? $this->serveUuid($request, $uuid, $size) : $this->serveDefault($size);
    }

    public function pngResponse(string $image): Response
    {
        return $this->responseFactory->make($image, Response::HTTP_OK, ['Content-Type' => 'image/png']);
    }

    protected function dispatchAccountImageServedEvent(): void
    {
        $this->eventDispatcher->dispatch(new AccountImageServedEvent($this->uuidResolver->getAccount()));
    }

    protected function cache(): CacheRepository
    {
        return \Cache::driver('file');
    }
}
