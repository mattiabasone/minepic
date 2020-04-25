<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use App\Models\AccountStats;
use App\Repositories\AccountRepository;
use App\Resolvers\UsernameResolver;
use App\Transformers\Account\AccountBasicDataTransformer;
use App\Transformers\Account\AccountTypeaheadTransformer;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JsonController extends BaseController
{
    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;
    /**
     * @var MinepicCore
     */
    private MinepicCore $minepicCore;
    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;
    /**
     * @var Manager
     */
    private Manager $dataManger;
    /**
     * @var UsernameResolver
     */
    private UsernameResolver $usernameResolver;

    /**
     * JsonController constructor.
     *
     * @param AccountRepository $accountRepository
     * @param MinepicCore       $minepicCore
     * @param Manager           $dataManger
     * @param ResponseFactory   $responseFactory
     * @param UsernameResolver  $usernameResolver
     */
    public function __construct(
        AccountRepository $accountRepository,
        MinepicCore $minepicCore,
        Manager $dataManger,
        ResponseFactory $responseFactory,
        UsernameResolver $usernameResolver
    ) {
        $this->accountRepository = $accountRepository;
        $this->minepicCore = $minepicCore;
        $this->dataManger = $dataManger;
        $this->responseFactory = $responseFactory;
        $this->usernameResolver = $usernameResolver;

        $this->dataManger->setSerializer(new ArraySerializer());
    }

    /**
     * User info.
     *
     * @param string $uuid
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function user($uuid = ''): JsonResponse
    {
        if (!$this->minepicCore->initialize($uuid)) {
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
        $resource = new Fractal\Resource\Item($account, new AccountBasicDataTransformer());

        $response = [
            'ok' => true,
            'data' => $this->dataManger->createData($resource)->toArray(),
        ];

        return $this->responseFactory->json($response, $httpStatus);
    }

    /**
     * @param string $username
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function userWithUsername(string $username): JsonResponse
    {
        $uuid = $this->usernameResolver->resolve($username);
        if ($uuid === env('DEFAULT_UUID')) {
            throw new NotFoundHttpException();
        }

        return $this->user($uuid);
    }

    /**
     * Update User data.
     *
     * @param string $uuidOrName
     *
     * @throws \Exception
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
        $accountsPagination = $this->accountRepository->filterPaginate(['term' => $term], 15);

        $resource = new Fractal\Resource\Collection(
            $accountsPagination->items(),
            new AccountTypeaheadTransformer()
        );

        return $this->responseFactory->json(
            $this->dataManger->createData($resource)->toArray()
        );
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
