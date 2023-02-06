<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers\Api;

use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use Minepic\Helpers\Storage\Files\SkinsStorage;
use Minepic\Image\Sections\Raw;
use Minepic\Minecraft\MinecraftDefaults;
use Minepic\Resolvers\UuidResolver;

class DownloadTextureController extends BaseController
{
    public function __construct(
        protected UuidResolver $uuidResolver,
        protected ResponseFactory $responseFactory
    ) {
    }

    /**
     * Download user texture.
     *
     * @param string $uuid User UUID or Username
     *
     * @throws \Minepic\Image\Exceptions\ImageCreateFromPngFailedException
     */
    public function serve(string $uuid = ''): Response
    {
        $headers = [
            'Content-Disposition' => 'Attachment;filename='.$uuid.'.png',
            'Content-Type' => 'image/png',
        ];
        $this->uuidResolver->resolve($uuid);

        $skinPath = $this->uuidResolver->resolve($uuid) ?
            SkinsStorage::getPath($this->uuidResolver->getUuid()) :
            SkinsStorage::getPath(MinecraftDefaults::STEVE_DEFAULT_SKIN_NAME);

        $rawSkin = new Raw($skinPath);
        $rawSkin->render();

        return $this->responseFactory->make((string) $rawSkin, 200, $headers);
    }
}
