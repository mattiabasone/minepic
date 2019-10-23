<?php

declare(strict_types=1);

namespace App;

use App\Database\Accounts;
use App\Database\AccountsNameChange;
use App\Database\AccountsNotFound;
use App\Database\AccountsStats;
use App\Helpers\Storage\Files\SkinsStorage;
use App\Image\IsometricAvatar;
use App\Image\Sections\Avatar;
use App\Image\Sections\Skin;
use App\Minecraft\MojangAccount;
use App\Minecraft\MojangClient;

class Core
{
    /**
     * Requested string.
     *
     * @var string
     */
    private $request = '';

    /**
     * Userdata from/to DB.
     *
     * @var Accounts
     */
    private $userdata;

    /**
     * Full userdata.
     *
     * @var MojangAccount
     */
    private $apiUserdata;

    /**
     * User data has been updated?
     *
     * @var bool
     */
    private $dataUpdated = false;

    /**
     * Set force update.
     *
     * @var bool
     */
    private $forceUpdate;

    /**
     * Minepic error string.
     *
     * @var string
     */
    private $error = false;

    /**
     * Account not found?
     *
     * @var bool
     */
    private $accountNotFound = false;

    /**
     * Retry for nonexistent usernames.
     *
     * @var string
     */
    private $retryUnexistentCheck = false;

    /**
     * Current image path.
     *
     * @var string
     */
    public $currentUserSkinImage;

    /**
     * Display error.
     *
     * @return string
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * Return current userdata.
     *
     * @return mixed
     */
    public function getApiUserdata(): MojangAccount
    {
        return $this->apiUserdata;
    }

    /**
     * Check if is a valid username.
     *
     * @param string
     *
     * @return bool
     */
    public function isValidUsername($username): bool
    {
        if (1 == \preg_match('#[^a-zA-Z0-9_]+#', $username)) {
            return false;
        }

        return true;
    }

    /**
     * Check if is a valid UUID.
     *
     * @param string
     *
     * @return bool
     */
    public function isValidUuid(): bool
    {
        $uuid = \mb_strtolower($this->request);
        if (1 == \preg_match('#[a-f0-9]{32}#', $uuid) && 32 == \mb_strlen($uuid)) {
            return true;
        }

        return false;
    }

    /**
     * Check if is an email address has invalid characters.
     *
     * @param string
     *
     * @return bool
     */
    public function isValidEmail($email): bool
    {
        if (1 == \preg_match('#^[a-zA-Z0-9\.\_\%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,8}$#', $email)) {
            return false;
        }

        return true;
    }

    /**
     * Normalize request.
     */
    private function normalizeRequest()
    {
        $this->request = \preg_replace("#\.png.*#", '', $this->request);
        $this->request = \preg_replace('#[^a-zA-Z0-9_]#', '', $this->request);
    }

    /**
     * Check if chache is still valid.
     *
     * @param int
     *
     * @return bool
     */
    private function checkDbCache(): bool
    {
        if ((\time() - $this->userdata->updated_at->timestamp) < env('USERDATA_CACHE_TIME')) {
            return true;
        }

        return false;
    }

    /**
     * Load saved userdata.
     *
     * @param string $type
     * @param string $value
     *
     * @return bool
     */
    private function loadDbUserdata($type = 'uuid', $value = ''): bool
    {
        if ('username' != $type) {
            $result = Accounts::where('uuid', $value)
                ->take(1)
                ->get();
        } else {
            $result = Accounts::where('username', $value)
                ->orderBy('username', 'desc')
                ->take(1)
                ->orderBy('updated_at', 'DESC')
                ->get();
        }

        if (\count($result) > 0) {
            $this->userdata = $result[0];
            $this->currentUserSkinImage = SkinsStorage::getPath($this->userdata->uuid);

            return true;
        }
        $this->currentUserSkinImage = SkinsStorage::getPath(env('DEFAULT_USERNAME'));

        return false;
    }

    /**
     * Return loaded userdata.
     *
     * @return Accounts
     */
    public function getUserdata(): ?Accounts
    {
        return $this->userdata;
    }

    /**
     * Get loaded userdata and stats (array).
     */
    public function getFullUserdata(): array
    {
        $userstats = AccountsStats::find($this->userdata->uuid);

        return [$this->userdata, $userstats];
    }

    /**
     * Check if an UUID is in the database.
     *
     * @param bool $uuid
     *
     * @return bool
     */
    private function uuidInDb($uuid = false): bool
    {
        if (!$uuid) {
            $uuid = $this->request;
        }

        return $this->loadDbUserdata('uuid', $uuid);
    }

    /**
     * Check if a username is in the database.
     *
     * @param mixed
     *
     * @return bool
     */
    private function nameInDb($name = false): bool
    {
        if (!$name) {
            $name = $this->request;
        }

        return $this->loadDbUserdata('username', $name);
    }

    /**
     * Insert userdata in database.
     *
     * @param void
     *
     * @return bool
     */
    public function insertNewUuid(): bool
    {
        if ($this->getFullUserdataApi()) {
            $this->userdata = new Accounts();
            $this->userdata->username = $this->apiUserdata->username;
            $this->userdata->uuid = $this->apiUserdata->uuid;
            $this->userdata->skin = ($this->apiUserdata->skin && \mb_strlen($this->apiUserdata->skin) > 1 ?
                $this->apiUserdata->skin : '');
            $this->userdata->cape = ($this->apiUserdata->cape && \mb_strlen($this->apiUserdata->cape) > 1 ?
                $this->apiUserdata->cape : '');
            $this->userdata->save();

            $this->saveRemoteSkin();
            $this->currentUserSkinImage = SkinsStorage::getPath($this->apiUserdata->uuid);

            $accountStats = new AccountsStats();
            $accountStats->uuid = $this->userdata->uuid;
            $accountStats->count_search = 0;
            $accountStats->count_request = 0;
            $accountStats->time_search = 0;
            $accountStats->time_request = 0;
            $accountStats->save();

            return true;
        }

        return false;
    }

    /**
     * Get UUID from username.
     *
     * @param string
     *
     * @return bool
     */
    private function convertRequestToUuid(): bool
    {
        if ($this->isValidUsername($this->request) || $this->isValidEmail($this->request)) {
            $MojangClient = new MojangClient();
            try {
                $account = $MojangClient->sendUsernameInfoRequest($this->request);
                $this->request = $account->uuid;

                return true;
            } catch (\Exception $e) {
                \Log::error($e);
                return false;
            }
        }

        return false;
    }

    /**
     * Salva account inesistente.
     *
     * @return mixed
     */
    public function saveUnexistentAccount()
    {
        $notFound = AccountsNotFound::firstOrNew(['request' => $this->request]);
        $notFound->request = $this->request;

        return $notFound->save();
    }

    /**
     * Check if requested string is a failed request.
     *
     * @param void
     *
     * @return bool
     */
    public function isUnexistentAccount(): bool
    {
        $result = AccountsNotFound::find($this->request);
        if (null != $result) {
            if ((\time() - $result->updated_at->timestamp) > env('USERDATA_CACHE_TIME')) {
                $this->retryUnexistentCheck = true;
            } else {
                $this->retryUnexistentCheck = false;
            }
            $this->accountNotFound = true;

            return true;
        }
        $this->accountNotFound = false;

        return false;
    }

    /**
     * Delete current request from failed cache.
     *
     * @return bool
     */
    public function removeFailedRequest(): bool
    {
        $result = AccountsNotFound::where('request', $this->request)->delete();

        return \count($result) > 0;
    }

    /**
     * HTTP Headers for current user.
     *
     * @param $size
     * @param string $type
     *
     * @return array
     */
    public function generateHttpCacheHeaders($size, $type = 'avatar'): array
    {
        if (isset($this->userdata->uuid) && $this->userdata->uuid !== '') {
            return [
                'Cache-Control' => 'private, max-age='.env('USERDATA_CACHE_TIME'),
                'Last-Modified' => \gmdate('D, d M Y H:i:s \G\M\T', $this->userdata->updated_at->timestamp),
                'Expires' => \gmdate('D, d M Y H:i:s \G\M\T', $this->userdata->updated_at->timestamp + env('USERDATA_CACHE_TIME')),
                'ETag' => \md5($type.$this->userdata->updated_at->timestamp.$this->userdata->uuid.$this->userdata->username.$size),
            ];
        }

        return [
            'Cache-Control' => 'private, max-age=7776000',
            'ETag' => \md5("{$type}_FFS_STOP_STEVE_SPAM_{$size}"),
            'Last-Modified' => \gmdate('D, d M Y H:i:s \G\M\T', \strtotime('2017-02-01 00:00')),
            'Expires' => \gmdate('D, d M Y H:i:s \G\M\T', \strtotime('2017-02-01 00:00')),
        ];
    }

    /**
     * Check requested string and initialize objects.
     *
     * @param string
     *
     * @return bool
     */
    public function initialize(string $string): bool
    {
        $this->dataUpdated = false;
        $this->request = $string;
        $this->normalizeRequest();

        if (!empty($this->request) && \mb_strlen($this->request) <= 32) {
            // TODO these checks needs optimizations
            // Valid UUID format? Then check if UUID is in my database
            if ($this->isValidUuid() && $this->uuidInDb()) {
                // Check if UUID is in my database
                // Data cache still valid?
                if (!$this->checkDbCache() || $this->forceUpdatePossible()) {
                    // Nope, updating data
                    $this->updateDbUser();
                } else {
                    // Check if local image exists
                    if (!SkinsStorage::exists($this->request)) {
                        $this->saveRemoteSkin();
                    }
                }

                return true;
            } elseif ($this->nameInDb()) {
                // Check DB datacache
                if (!$this->checkDbCache() || $this->forceUpdatePossible()) {
                    // Check UUID (username change/other)
                    if ($this->convertRequestToUuid()) {
                        if ($this->request === $this->userdata->uuid) {
                            // Nope, updating data
                            $this->request = $this->userdata->uuid;
                            $this->updateDbUser();
                        } else {
                            // re-initialize process with the UUID if the name has been changed
                            return $this->initialize($this->request);
                        }
                    } else {
                        $this->request = $this->userdata->uuid;
                        $this->updateUserFailUpdate();
                        SkinsStorage::copyAsSteve($this->request);
                    }
                } else {
                    // Check if local image exists
                    if (!SkinsStorage::exists($this->request)) {
                        SkinsStorage::copyAsSteve($this->request);
                    }
                }

                return true;
            } else {
                // Account not found? time to retry to get information from Mojang?
                if (!$this->isUnexistentAccount() || $this->retryUnexistentCheck) {
                    if (!$this->isValidUuid() && !$this->convertRequestToUuid()) {
                        $this->saveUnexistentAccount();
                        $this->userdata = null;
                        $this->currentUserSkinImage = SkinsStorage::getPath(env('DEFAULT_USERNAME'));
                        $this->error = 'Invalid request username';
                        $this->request = '';

                        return false;
                    }

                    // Check if the uuid is already in the database, maybe the user has changed username and the check
                    // nameInDb() has failed
                    if ($this->uuidInDb()) {
                        $this->updateDbUser();

                        return true;
                    }

                    if ($this->insertNewUuid()) {
                        if ($this->accountNotFound) {
                            $this->removeFailedRequest();
                        }

                        return true;
                    }
                }
            }
        }

        $this->userdata = null;
        $this->currentUserSkinImage = SkinsStorage::getPath(env('DEFAULT_USERNAME'));
        $this->error = 'Account not found';
        $this->request = '';

        return false;
    }

    /**
     * Update current user fail count.
     *
     * @return bool
     */
    private function updateUserFailUpdate(): bool
    {
        if (isset($this->userdata->uuid)) {
            ++$this->userdata->fail_count;

            return $this->userdata->save();
        }

        return false;
    }

    /**
     * Update db userdata.
     *
     * @return bool
     */
    private function updateDbUser(): bool
    {
        if (isset($this->userdata->username) && '' != $this->userdata->uuid) {
            // Get data from API
            if ($this->getFullUserdataApi()) {
                $originalUsername = $this->userdata->username;

                // Update database
                $this->userdata->username = $this->apiUserdata->username;
                $this->userdata->skin = $this->apiUserdata->skin;
                $this->userdata->cape = $this->apiUserdata->cape;
                $this->userdata->fail_count = 0;
                $this->userdata->save();

                // Update skin
                $this->saveRemoteSkin();

                // Log username change
                if ($this->userdata->username != $originalUsername && '' != $originalUsername) {
                    $this->logUsernameChange($originalUsername, $this->userdata->username, $this->userdata->uuid);
                }
                $this->dataUpdated = true;

                return true;
            }

            $this->updateUserFailUpdate();

            if (!SkinsStorage::exists($this->userdata->uuid)) {
                SkinsStorage::copyAsSteve($this->userdata->uuid);
            }
        }
        $this->dataUpdated = false;

        return false;
    }

    /**
     * Return if data has been updated.
     *
     * @return bool
     */
    public function userDataUpdated(): bool
    {
        return $this->dataUpdated;
    }

    /**
     * Log the username change.
     *
     * @param $prev string Previous username
     * @param $new string New username
     * @param $uuid string User UUID
     *
     * @return bool
     */
    private function logUsernameChange(string $prev, string $new, string $uuid): bool
    {
        $accountNameChange = new AccountsNameChange();
        $accountNameChange->uuid = $uuid;
        $accountNameChange->prev_name = $prev;
        $accountNameChange->new_name = $new;
        $accountNameChange->time_change = \time();

        return $accountNameChange->save();
    }

    /**
     * Get userdata from Mojang API.
     *
     * @param mixed
     *
     * @return bool
     */
    private function getFullUserdataApi(): bool
    {
        $MojangClient = new MojangClient();
        try {
            $this->apiUserdata = $MojangClient->getUuidInfo($this->request);

            return true;
        } catch (\Exception $e) {
            \Log::error($e);
            $this->apiUserdata = null;

            return false;
        }
    }

    /*==================================================================================================================
     * =AVATAR
     *================================================================================================================*/

    /**
     * Show rendered avatar.
     *
     * @param int
     * @param mixed
     *
     * @return Avatar
     */
    public function avatarCurrentUser(int $size = 0): Avatar
    {
        $avatar = new Avatar($this->currentUserSkinImage);
        $avatar->renderAvatar($size);

        return $avatar;
    }

    /**
     * Random avatar from saved.
     *
     * @param int
     *
     * @return Avatar
     */
    public function randomAvatar(int $size = 0): Avatar
    {
        $all_skin = \scandir(storage_path(env('SKINS_FOLDER')));
        $rand = \random_int(2, \count($all_skin));

        $avatar = new Avatar(SkinsStorage::getPath($all_skin[$rand]));
        $avatar->renderAvatar($size);

        return $avatar;
    }

    /**
     * Default Avatar.
     *
     * @param int $size
     *
     * @return Avatar (rendered)
     */
    public function defaultAvatar(int $size = 0): Avatar
    {
        $avatar = new Avatar(SkinsStorage::getPath(env('DEFAULT_USERNAME')));
        $avatar->renderAvatar($size);

        return $avatar;
    }

    /*==================================================================================================================
     * =ISOMETRIC_AVATAR
     *================================================================================================================*/

    /**
     * Default Avatar Isometric.
     *
     * @param int $size
     *
     * @return IsometricAvatar
     */
    public function isometricAvatarCurrentUser(int $size = 0): IsometricAvatar
    {
        // TODO: Needs refactoring
        $uuid = $this->userdata->uuid ?? env('DEFAULT_UUID');
        $timestamp = $this->userdata->updated_at->timestamp ?? time();
        $isometricAvatar = new IsometricAvatar(
            $uuid,
            $timestamp
        );
        $isometricAvatar->render($size);

        return $isometricAvatar;
    }

    /**
     * Default Avatar (Isometric).
     *
     * @param int $size
     *
     * @return IsometricAvatar (rendered)
     */
    public function defaultIsometricAvatar(int $size = 0): IsometricAvatar
    {
        $isometricAvatar = new IsometricAvatar(
            env('DEFAULT_UUID'),
            0
        );
        $isometricAvatar->checkCacheStatus(false);
        $isometricAvatar->render($size);

        return $isometricAvatar;
    }

    /*==================================================================================================================
     * =SKIN
     *================================================================================================================*/

    /**
     * Save skin image.
     *
     * @param mixed
     *
     * @return bool
     */
    public function saveRemoteSkin(): bool
    {
        if (!empty($this->userdata->skin) && \mb_strlen($this->userdata->skin) > 0) {
            $mojangClient = new MojangClient();
            try {
                $skinData = $mojangClient->getSkin($this->userdata->skin);

                return SkinsStorage::save($this->userdata->uuid, $skinData);
            } catch (\Exception $e) {
                \Log::error($e);
                $this->error = $e->getMessage();
            }
        }

        return SkinsStorage::copyAsSteve($this->userdata->uuid);
    }

    /**
     * Return rendered skin.
     *
     * @param int
     * @param string
     *
     * @return Skin
     *
     * @throws \Throwable
     */
    public function renderSkinCurrentUser(int $size = 0, string $type = 'F'): Skin
    {
        $skin = new Skin($this->currentUserSkinImage);
        $skin->renderSkin($size, $type);

        return $skin;
    }

    /**
     * Return a Skin object of the current user.
     *
     * @return Skin
     */
    public function skinCurrentUser(): Skin
    {
        return new Skin($this->currentUserSkinImage);
    }

    /**
     * Set force update.
     *
     * @param bool $forceUpdate
     */
    public function setForceUpdate(bool $forceUpdate)
    {
        $this->forceUpdate = $forceUpdate;
    }

    /**
     * Can I exec force update?
     *
     * @return bool
     */
    private function forceUpdatePossible(): bool
    {
        return
            ($this->forceUpdate) &&
            ((\time() - $this->userdata->updated_at->timestamp) > env('MIN_USERDATA_UPDATE_INTERVAL'))
            ;
    }

    /*==================================================================================================================
     * =STATS
     *================================================================================================================*/

    /**
     * Use steve skin for given username.
     *
     * @param string
     */
    public function updateStats($type = 'request')
    {
        if (env('STATS_ENABLED') && !empty($this->userdata->uuid) && $this->userdata->uuid !== env('DEFAULT_UUID')) {
            $AccStats = new AccountsStats();
            if ('request' == $type) {
                $AccStats->incrementRequestStats($this->userdata->uuid);
            } elseif ('search' == $type) {
                $AccStats->incrementSearchStats($this->userdata->uuid);
            }
        }
    }
}
