<?php

declare(strict_types=1);

namespace Minepic\Account\Transformers;

use App\Models\Account;
use League\Fractal;

/**
 * Class AccountTypeaheadTransformer.
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
