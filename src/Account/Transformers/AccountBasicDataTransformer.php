<?php

declare(strict_types=1);

namespace Minepic\Account\Transformers;

use App\Models\Account;
use League\Fractal;

/**
 * Class AccountTypeaheadTransformer.
 */
class AccountBasicDataTransformer extends Fractal\TransformerAbstract
{
    public function transform(Account $account): array
    {
        return [
            'uuid' => $account->uuid,
            'username' => $account->username,
            //'count_request' => $accountStats->count_request,
            //'count_search' => $accountStats->count_search,
            //'last_request' => DateHelper::humanizeTimestamp($accountStats->time_request),
            //'last_search' => DateHelper::humanizeTimestamp($accountStats->time_search),
        ];
    }
}
