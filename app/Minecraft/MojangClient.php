<?php

declare(strict_types=1);

namespace App\Minecraft;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;

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
     * Last API Response.
     *
     * @var
     */
    private $lastResponse;

    /**
     * Last Error.
     *
     * @var string
     */
    private string $lastError = '';

    /**
     * Last error code.
     *
     * @var int
     */
    private int $lastErrorCode = 0;

    /**
     * Last content Type.
     *
     * @var string
     */
    private string $lastContentType = '';

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

    /**
     * Last response from API.
     *
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    private function handleGuzzleBadResponseException(
        BadResponseException $badResponseException
    ): void {
        $this->lastContentType = $badResponseException->getResponse()->getHeader('content-type')[0] ?? '';
        $this->lastErrorCode = $badResponseException->getResponse()->getStatusCode();
        $this->lastError = 'Error';
        if (isset($this->lastResponse['errorMessage'])) {
            $this->lastError .= ': '.$this->lastResponse['errorMessage'];
        }
    }

    private function handleThrowable(\Throwable $exception): void
    {
        $this->lastContentType = '';
        $this->lastErrorCode = 0;
        $this->lastError = $exception->getFile().':'.$exception->getLine().' - '.$exception->getMessage();
    }

    /**
     * Send new request.
     *
     * @param string $method HTTP Verb
     * @param string $url API Endpoint
     * @return bool
     */
    private function sendApiRequest(string $method, string $url): bool
    {
        try {
            $response = $this->httpClient->request($method, $url);
            $this->lastResponse = json_decode($response->getBody()->getContents(), true);
            $this->lastContentType = $response->getHeader('content-type')[0];
            $this->lastErrorCode = 0;
            $this->lastError = '';

            return true;
        } catch (BadResponseException $exception) {
            $this->lastResponse = json_decode($exception->getResponse()->getBody()->getContents(), true);
            $this->handleGuzzleBadResponseException($exception);

            return false;
        } catch (\Throwable $exception) {
            $this->handleThrowable($exception);

            return false;
        }
    }

    /**
     * Generic request.
     * @param string $method
     * @param string $url
     * @return bool
     */
    private function sendRequest(string $method, string $url): bool
    {
        try {
            $response = $this->httpClient->request($method, $url);
            $this->lastResponse = $response->getBody()->getContents();
            $this->lastContentType = $response->getHeader('content-type')[0];
            $this->lastErrorCode = 0;
            $this->lastError = '';

            return true;
        } catch (BadResponseException $exception) {
            $this->lastResponse = $exception->getResponse()->getBody()->getContents();
            $this->handleGuzzleBadResponseException($exception);

            return false;
        } catch (\Throwable $exception) {
            $this->handleThrowable($exception);

            return false;
        }
    }

    /**
     * Account info from username.
     *
     * @param string $username
     * @return MojangAccount
     * @throws \Exception
     */
    public function sendUsernameInfoRequest(string $username): MojangAccount
    {
        if ($this->sendApiRequest('GET', env('MINECRAFT_PROFILE_URL').$username)) {
            return new MojangAccount([
                'username' => $this->lastResponse['name'],
                'uuid' => $this->lastResponse['id'],
            ]);
        }
        throw new \Exception($this->lastError, $this->lastErrorCode);
    }

    /**
     * Account info from UUID.
     *
     * @param string $uuid User UUID
     * @return MojangAccount
     * @throws \Exception
     */
    public function getUuidInfo(string $uuid): MojangAccount
    {
        if ($this->sendApiRequest('GET', env('MINECRAFT_SESSION_URL').$uuid)) {
            $account = new MojangAccount();
            if ($account->loadFromApiResponse($this->lastResponse)) {
                return $account;
            }
            throw new \Exception('Cannot create data account');
        }
        throw new \Exception($this->lastError, $this->lastErrorCode);
    }

    /**
     * Get Skin.
     *
     * @param string $skin Skin uuid
     * @throws \Exception
     */
    public function getSkin(string $skin)
    {
        if ($this->sendRequest('GET', env('MINECRAFT_TEXTURE_URL').$skin)) {
            if ($this->lastContentType === 'image/png') {
                return $this->lastResponse;
            }
            throw new \Exception('Invalid format: ');
        }
        throw new \Exception($this->lastError, $this->lastErrorCode);
    }
}
