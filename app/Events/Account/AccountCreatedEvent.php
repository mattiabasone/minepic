<?php

declare(strict_types=1);

namespace App\Events\Account;

use App\Events\Event;
use App\Models\Account;

class AccountCreatedEvent extends Event
{
    /**
     * @var Account|null
     */
    private ?Account $account;

    /**
     * @param Account|null $account
     */
    public function __construct(?Account $account)
    {
        $this->account = $account;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }
}
