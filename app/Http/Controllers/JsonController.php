<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use Minepic\Exceptions\NotFoundHttpJsonException;
use Minepic\Models\Account;
use Minepic\Models\AccountStats;
use Minepic\Repositories\AccountRepository;
use Minepic\Resolvers\UsernameResolver;
use Minepic\Resolvers\UuidResolver;
use Minepic\Transformers\Account\AccountBasicDataTransformer;
use Minepic\Transformers\Account\AccountTypeaheadTransformer;

class JsonController extends BaseController
{
    private const USER_NOT_FOUND_MESSAGE = 'User not found';

    /**
     * @param AccountRepository $accountRepository
     * @param UuidResolver $uuidResolver
     * @param Manager $dataManger
     * @param ResponseFactory $responseFactory
     * @param UsernameResolver $usernameResolver
     */
    public function __construct(
        private AccountRepository $accountRepository,
        private UuidResolver      $uuidResolver,
        private Manager           $dataManger,
        private ResponseFactory   $responseFactory,
        private UsernameResolver  $usernameResolver
    )
    {
        $this->dataManger->setSerializer(new ArraySerializer());
    }

    /**
     * User info.
     *
     * @param string $uuid
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function user($uuid = ''): JsonResponse
    {
        if (!$this->uuidResolver->resolve($uuid)) {
            $httpStatus = 404;
            $response = [
                'ok' => false,
                'message' => self::USER_NOT_FOUND_MESSAGE,
            ];

            return $this->responseFactory->json($response, $httpStatus);
        }

        $httpStatus = 200;
        $account = $this->uuidResolver->getAccount();
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
     * @return JsonResponse
     * @throws \Throwable
     *
     */
    public function userWithUsername(string $username): JsonResponse
    {
        $uuid = $this->usernameResolver->resolve($username);
        if ($uuid === null) {
            throw new NotFoundHttpJsonException(self::USER_NOT_FOUND_MESSAGE);
        }

        return $this->user($uuid);
    }

    /**
     * Update User data.
     *
     * @param string $uuid
     *
     * @return JsonResponse
     * @throws \Throwable
     *
     */
    public function updateUser(string $uuid): JsonResponse
    {
        // Force user update
        $this->uuidResolver->setForceUpdate(true);

        // Check if user exists
        if (!$this->uuidResolver->resolve($uuid)) {
            return $this->responseFactory->json(
                ['ok' => false, 'message' => self::USER_NOT_FOUND_MESSAGE],
                Response::HTTP_NOT_FOUND
            );
        }

        // Check if data has been updated
        if ($this->uuidResolver->userDataUpdated()) {
            return $this->responseFactory->json(
                ['ok' => true, 'message' => 'Data updated'],
                Response::HTTP_OK
            );
        }

        $userdata = $this->uuidResolver->getAccount();
        $dateString = $userdata->updated_at->toW3cString();

        $response = [
            'ok' => false,
            'message' => 'Cannot update user, userdata has been updated recently',
            'last_update' => $dateString,
        ];

        return $this->responseFactory->json($response, Response::HTTP_FORBIDDEN);
    }

    /**
     * Username Typeahead.
     *
     * @param string $username
     *
     * @return JsonResponse
     */
    public function userTypeahead(string $username): JsonResponse
    {
        $accountsPagination = $this->accountRepository->filterPaginate(['term' => $username], 15);

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
     *
     * @return JsonResponse
     */
    public function getMostWantedUsers(): JsonResponse
    {
        return $this->responseFactory->json([
            'ok' => true,
            'data' => AccountStats::getMostWanted(),
        ]);
    }

    /**
     * Check system status
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $account = Account::whereUuid('be1cac3b60f04e0dba12c77cc8e0ec21')
            ->take(1)
            ->first();

        return $this->responseFactory->json([
            'ok' => $account instanceof Account,
        ]);
    }
}
