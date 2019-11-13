<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core as MinepicCore;
use App\Models\Account;
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
     * @param Request $request    Injected Request
     * @param string  $uuidOrName
     * @param int     $size       Avatar size User UUID or name
     *
     * @return Response
     */
    abstract public function serve(Request $request, $uuidOrName = '', $size = 0): Response;

    /**
     * Isometric Avatar with Size.
     *
     * @param Request $request    Injected Request
     * @param int     $size       Avatar size
     * @param string  $uuidOrName User UUID or name
     *
     * @return Response
     */
    public function serveWithSize(Request $request, $size = 0, $uuidOrName = ''): Response
    {
        return $this->serve($request, $uuidOrName, $size);
    }

    /**
     * HTTP Headers for current user.
     *
     * @param Account $account
     * @param $size
     * @param string $type
     *
     * @return array
     */
    public function generateHttpCacheHeaders(?Account $account, $size, $type = 'avatar'): array
    {
        if ($account !== null && $account->uuid !== null) {
            return [
                'Cache-Control' => 'private, max-age='.env('USERDATA_CACHE_TIME'),
                'Last-Modified' => \gmdate('D, d M Y H:i:s \G\M\T', $account->updated_at->timestamp),
                'Expires' => \gmdate('D, d M Y H:i:s \G\M\T', $account->updated_at->timestamp + env('USERDATA_CACHE_TIME')),
                'ETag' => \md5($type.$account->updated_at->timestamp.$account->uuid.$account->username.$size),
            ];
        }

        return [
            'Cache-Control' => 'private, max-age=7776000',
            'ETag' => \md5("{$type}_FFS_STOP_STEVE_SPAM_{$size}"),
            'Last-Modified' => \gmdate('D, d M Y H:i:s \G\M\T', \strtotime('2017-02-01 00:00')),
            'Expires' => \gmdate('D, d M Y H:i:s \G\M\T', \strtotime('2017-02-01 00:00')),
        ];
    }
}
