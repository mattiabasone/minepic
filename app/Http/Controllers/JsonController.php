<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use App\Helpers\Date as DateHelper;
use App\Models\AccountStats;
use App\Repositories\AccountRepository;
use App\Repositories\AccountStatsRepository;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    /**
     * @var AccountRepository
     */
    private $accountRepository;
    /**
     * @var AccountStatsRepository
     */
    private $accountStatsRepository;
    /**
     * @var Manager
     */
    private $dataManger;

    public function __construct(
        AccountRepository $accountRepository,
        AccountStatsRepository $accountStatsRepository,
        MinepicCore $minepicCore,
        Manager $dataManger,
        ResponseFactory $responseFactory
    ) {
        $this->accountRepository = $accountRepository;
        $this->accountStatsRepository = $accountStatsRepository;
        $this->minepicCore = $minepicCore;
        $this->dataManger = $dataManger;
        $this->responseFactory = $responseFactory;

        $this->dataManger->setSerializer(new ArraySerializer());
    }

    /**
     * User info.
     *
     * @param string $uuidOrName
     * @return JsonResponse
     * @throws \Exception
     */
    public function user($uuidOrName = ''): JsonResponse
    {
        if (!$this->minepicCore->initialize($uuidOrName)) {
            $httpStatus = 404;
            $response = [
                'ok' => false,
                'message' => 'User not found',
            ];

            return $this->responseFactory->json($response, $httpStatus);
        }

        $httpStatus = 200;
        $account = $this->minepicCore->getUserdata();

        if ($account === null) {
            throw new NotFoundHttpException();
        }

        $accountStats = $this->accountStatsRepository->findByUuid($account->uuid);

        $response = [
            'ok' => true,
            'userdata' => [
                'uuid' => $account->uuid,
                'username' => $account->username,
                'count_request' => $accountStats->count_request,
                'count_search' => $accountStats->count_search,
                'last_request' => DateHelper::humanizeTimestamp($accountStats->time_request),
                'last_search' => DateHelper::humanizeTimestamp($accountStats->time_search),
            ],
        ];

        return $this->responseFactory->json($response, $httpStatus);
    }

    /**
     * Update User data.
     * @param string $uuidOrName
     * @return JsonResponse
     * @throws \Exception
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
     * @return JsonResponse
     */
    public function userTypeahead($term): JsonResponse
    {
        $response = [];
        $accounts = $this->accountRepository->filterPaginate(['term' => $term], 15);
        // TODO: migrate to transformers
        foreach ($accounts->items() as $account) {
            $response[]['value'] = $account->username;
        }

        return $this->responseFactory->json($response);
    }

    /**
     * Get most wanted account list.
     */
    public function getMostWantedUsers(): JsonResponse
    {
        return $this->responseFactory->json([
            'ok' => true,
            'data' => AccountStats::getMostWanted(),
        ]);
    }
}
