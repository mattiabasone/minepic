<?php

declare(strict_types=1);

namespace App\Events\Account;

use App\Events\Event;

class UsernameChangeEvent extends Event
{
    /**
     * @var string
     */
    private string $uuid;
    /**
     * @var string
     */
    private string $previousName;
    /**
     * @var string
     */
    private string $newName;

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
