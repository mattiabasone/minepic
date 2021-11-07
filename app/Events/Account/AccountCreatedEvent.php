<?php

declare(strict_types=1);

namespace Minepic\Events\Account;

use Minepic\Events\Event;
use Minepic\Models\Account;

class AccountCreatedEvent extends Event
{
    /**
     * @var Account
     */
    private Account $account;

    /**
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
}
