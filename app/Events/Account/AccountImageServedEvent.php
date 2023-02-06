<?php

declare(strict_types=1);

namespace Minepic\Events\Account;

use Minepic\Events\Event;
use Minepic\Models\Account;

class AccountImageServedEvent extends Event
{
    public function __construct(private Account $account)
    {
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
}
