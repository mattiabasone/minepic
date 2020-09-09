<?php

declare(strict_types=1);

namespace Minepic\Transformers\Account;

use League\Fractal;
use Minepic\Models\Account;

class AccountTypeaheadTransformer extends Fractal\TransformerAbstract
{
    public function transform(Account $account): array
    {
        return [
            'uuid' => $account->uuid,
            'username' => $account->username,
        ];
    }
}
