<?php

declare(strict_types=1);

namespace App\Transformers\Account;

use App\Models\Account;
use Carbon\Carbon;
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
            'count_request' => $account->stats->count_request,
            'count_search' => $account->stats->count_search,
            'last_request' => Carbon::createFromTimestamp($account->stats->time_request)->format(Carbon::ATOM),
            'last_search' => Carbon::createFromTimestamp($account->stats->time_search)->format(Carbon::ATOM),
        ];
    }
}
