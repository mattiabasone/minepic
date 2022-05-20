<?php

declare(strict_types=1);

namespace Minepic\Events\Account;

use Minepic\Events\Event;

class UsernameChangeEvent extends Event
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $previousName,
        private readonly string $newName
    ) {
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
