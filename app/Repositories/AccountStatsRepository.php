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
     * Find account using UUID
     *
     * @param string $uuid User UUID
     * @param array $columns Which columns should be loaded
     * @return \App\Models\AccountStats Statistics related to given UUID
     */
    public function findByUuid(string $uuid, $columns = ['*']): AccountStats
    {
        return $this->findBy('uuid', $uuid, $columns);
    }

    /**
     * Increment request counter.
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

    /**
     * Increment search counter.
     * @param string $uuid User UUID
     */
    public function incrementSearchCounter(string $uuid = ''): void
    {
        $this->query()
            ->where('uuid', $uuid)
            ->update([
                'count_search' => app('db')->raw('count_search + 1'),
                'time_search' => \time(),
            ]);
    }
}
