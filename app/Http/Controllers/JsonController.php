<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use App\Database\Accounts;
use App\Database\AccountsStats;
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
    /**
     * @var MinepicCore
     */
    private $minepicCore;

    public function __construct(MinepicCore $minepicCore, ResponseFactory $responseFactory)
    {
        $this->minepicCore = $minepicCore;
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
        if ($this->minepicCore->initialize($uuidOrName)) {
            $httpStatus = 200;
            [$userdata, $userstats] = $this->minepicCore->getFullUserdata();

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
     * Update User data.
     *
     * @param string $uuidOrName
     *
     * @return JsonResponse
     */
    public function updateUser(string $uuidOrName): JsonResponse
    {
        // Force user update
        $this->minepicCore->setForceUpdate(true);

        // Check if user exists
        if ($this->minepicCore->initialize($uuidOrName)) {
            // Check if data has been updated
            if ($this->minepicCore->userDataUpdated()) {
                $response = ['ok' => true, 'message' => 'Data updated'];
                $httpStatus = 200;
            } else {
                $userdata = $this->minepicCore->getUserdata();
                $dateString = $userdata->updated_at->toW3cString();

                $response = [
                    'ok' => false,
                    'message' => 'Cannot update user, userdata has been updated recently',
                    'last_update' => $dateString,
                ];

                $httpStatus = 403;
            }
        } else {
            $response = ['ok' => false, 'message' => 'User not found'];
            $httpStatus = 404;
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

    /**
     * @return JsonResponse
     */
    public function getMostWantedUsers(): JsonResponse
    {
        return $this->responseFactory->json([
            'ok' => true,
            'data' => AccountsStats::getMostWanted(),
        ]);
    }
}
