<?php

declare(strict_types=1);

namespace Minepic\Minecraft;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;
use Minepic\Minecraft\Exceptions\UserNotFoundException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MojangClient.
 */
class MojangClient
{
    /**
     * User Agent used for requests.
     */
    private const USER_AGENT = 'Minepic/2.0 (minepic.org)';

    /**
     * HTTP Client for requests.
     *
     * @var \GuzzleHttp\Client
     */
    private \GuzzleHttp\Client $httpClient;

    /**
     * MojangClient constructor.
     */
    public function __construct()
    {
        $this->httpClient = new HttpClient(
            [
                'headers' => [
                    'User-Agent' => self::USER_AGENT,
                ],
            ]
        );
    }

    private function handleGuzzleBadResponseException(
        BadResponseException $badResponseException
    ): void {
        Log::error(
            'Error from Minecraft API',
            [
                'status_code' => $badResponseException->getResponse()->getStatusCode(),
                'content_type' => $badResponseException->getResponse()->getHeader('content-type')[0] ?? '',
                'content' => $badResponseException->getResponse()->getBody()->getContents(),
            ]
        );
    }

    /**
     * @param \Throwable $exception
     */
    private function handleThrowable(\Throwable $exception): void
    {
        Log::error($exception->getFile().':'.$exception->getLine().' - '.$exception->getMessage());
        Log::error($exception->getTraceAsString());
    }

    /**
     * Send new request.
     *
     * @param string $method HTTP Verb
     * @param string $url    API Endpoint
     *
     * @throws \Throwable
     *
     * @return array
     */
    private function sendApiRequest(string $method, string $url): ?array
    {
        try {
            $response = $this->httpClient->request($method, $url);
            // No Content
            if ($response->getStatusCode() === 204) {
                return null;
            }

            $responseContents = $response->getBody()->getContents();
            Log::debug('Minecraft API Response: '.$responseContents, ['method' => $method, 'url' => $url]);

            return \json_decode($responseContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (BadResponseException $exception) {
            $this->handleGuzzleBadResponseException($exception);

            throw $exception;
        } catch (\Throwable $exception) {
            $this->handleThrowable($exception);

            throw $exception;
        }
    }

    /**
     * Generic request.
     *
     * @param string $method
     * @param string $url
     *
     * @throws \Throwable
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function sendRequest(string $method, string $url): ResponseInterface
    {
        try {
            return $this->httpClient->request($method, $url);
        } catch (BadResponseException $exception) {
            $this->handleGuzzleBadResponseException($exception);

            throw $exception;
        } catch (\Throwable $exception) {
            $this->handleThrowable($exception);

            throw $exception;
        }
    }

    /**
     * Account info from username.
     *
     * @param string $username
     *
     * @throws \Throwable
     *
     * @return MojangAccount
     */
    public function sendUsernameInfoRequest(string $username): MojangAccount
    {
        $response = $this->sendApiRequest('GET', env('MINECRAFT_PROFILE_URL').$username);

        if ($response !== null) {
            return new MojangAccount($response['id'], $response['name']);
        }

        throw new UserNotFoundException("Unknown user {$username}");
    }

    /**
     * Account info from UUID.
     *
     * @param string $uuid User UUID
     *
     * @throws \Throwable
     *
     * @return MojangAccount
     */
    public function getUuidInfo(string $uuid): MojangAccount
    {
        $response = $this->sendApiRequest('GET', env('MINECRAFT_SESSION_URL').$uuid);

        if ($response === null) {
            throw new \Exception('Cannot create data account');
        }

        return MojangAccountFactory::makeFromApiResponse($response);
    }

    /**
     * Get Skin.
     *
     * @param string $skin Skin uuid
     *
     * @throws \Exception|\Throwable
     *
     * @return string
     */
    public function getSkin(string $skin): string
    {
        $response = $this->sendRequest('GET', env('MINECRAFT_TEXTURE_URL').$skin);

        if ($response->getHeader('content-type')[0] === 'image/png') {
            return $response->getBody()->getContents();
        }

        throw new \Exception('Invalid Response content type: '.$response->getHeader('content-type')[0]);
    }
}
