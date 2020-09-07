<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AccountStats;

class AccountStatsRepository
{
    public function model(): string
    {
        return AccountStats::class;
    }
}
