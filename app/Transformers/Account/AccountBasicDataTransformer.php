<?php

declare(strict_types=1);

namespace App\Transformers\Account;

use App\Models\Account;
use Carbon\Carbon;
use League\Fractal;

class AccountBasicDataTransformer extends Fractal\TransformerAbstract
{
    public function transform(Account $account): array
    {
        return [
            'uuid' => $account->uuid,
            'username' => $account->username,
            'count_request' => $account->stats->count_request,
            'last_request' => $account->stats->request_at ?
                $account->stats->request_at->format(Carbon::ATOM) :
                null,
        ];
    }
}
