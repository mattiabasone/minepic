<?php

namespace Minepic\Account\Transformers;

use League\Fractal;
use App\Models\Account;

/**
 * Class AccountTypeaheadTransformer
 * @package Minepic\Account\Transformers
 */
class AccountTypeaheadTransformer extends Fractal\TransformerAbstract
{
    public function transform(Account $account): array
    {
        return [
            'value' => $account->username,
        ];
    }
}