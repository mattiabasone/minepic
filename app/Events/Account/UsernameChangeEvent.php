<?php

namespace App\Events\Account;

use App\Events\Event;

/**
 * Class UsernameChangeEvent
 * @package App\Events\Account
 */
class UsernameChangeEvent extends Event
{
    /**
     * @var string
     */
    private $uuid;
    /**
     * @var string
     */
    private $previousName;
    /**
     * @var string
     */
    private $newName;

    public function __construct(string $uuid, string $previousName, string $newName)
    {
        $this->uuid = $uuid;
        $this->previousName = $previousName;
        $this->newName = $newName;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getPreviousName(): string
    {
        return $this->previousName;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }
}
