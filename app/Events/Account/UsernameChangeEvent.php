<?php

declare(strict_types=1);

namespace App\Events\Account;

use App\Events\Event;

/**
 * Class UsernameChangeEvent.
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

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPreviousName(): string
    {
        return $this->previousName;
    }

    public function getNewName(): string
    {
        return $this->newName;
    }
}
