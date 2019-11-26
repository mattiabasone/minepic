<?php

namespace Minepic\Account\Transformers;

use League\Fractal;
use App\Models\Account;
use App\Helpers\Date as DateHelper;

/**
 * Class AccountTypeaheadTransformer
 * @package Minepic\Account\Transformers
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