<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AccountStats;
use Czim\Repository\BaseRepository;

/**
 * Class AccountStatsRepository.
 */
class AccountStatsRepository extends BaseRepository
{
    public function model(): string
    {
        return AccountStats::class;
    }

    /**
     * Increment request counter.
     *
     * @param string $uuid User UUID
     */
    public function incrementRequestCounter(string $uuid = ''): void
    {
        $this->query()
            ->where('uuid', $uuid)
            ->update([
                'count_request' => app('db')->raw('count_request + 1'),
                'time_request' => \time(),
            ]);
    }
}
