<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use App\Database\Accounts;
use App\Helpers\Date as DateHelper;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;

class JsonController extends BaseController
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * User info.
     *
     * @param string $uuidOrName
     *
     * @return JsonResponse
     */
    public function user($uuidOrName = ''): JsonResponse
    {
        $minepicCore = new MinepicCore();
        if ($minepicCore->initialize($uuidOrName)) {
            $httpStatus = 200;
            [$userdata, $userstats] = $minepicCore->getFullUserdata();

            $response = [
                'ok' => true,
                'userdata' => [
                    'uuid' => $userdata->uuid,
                    'username' => $userdata->username,
                    'count_request' => $userstats->count_request,
                    'count_search' => $userstats->count_search,
                    'last_request' => DateHelper::humanizeTimestamp($userstats->time_request),
                    'last_search' => DateHelper::humanizeTimestamp($userstats->time_search),
                ],
            ];
        } else {
            $httpStatus = 404;
            $response = [
                'ok' => false,
                'message' => 'User not found',
            ];
        }

        return $this->responseFactory->json($response, $httpStatus);
    }

    /**
     * Username Typeahead.
     *
     * @param $term
     *
     * @return JsonResponse
     */
    public function userTypeahead($term): JsonResponse
    {
        $response = [];
        $accounts = Accounts::query()
            ->select(['username'])
            ->where('username', 'LIKE', $term.'%')
            ->take(15)
            ->get();
        foreach ($accounts as $account) {
            $response[]['value'] = $account->username;
        }

        return $this->responseFactory->json($response);
    }
}
