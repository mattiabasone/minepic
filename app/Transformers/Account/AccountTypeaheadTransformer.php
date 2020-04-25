<?php

declare(strict_types=1);

namespace App\Transformers\Account;

use App\Models\Account;
use League\Fractal;

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
