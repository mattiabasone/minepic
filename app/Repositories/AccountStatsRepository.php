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
     * @param string $uuid
     *
     * @return \Illuminate\Database\Eloquent\Model|AccountStats|null
     */
    public function createEmptyStatsForUuid(string $uuid): ?AccountStats
    {
        return $this->create([
            'uuid' => $uuid,
            'count_search' => 0,
            'count_request' => 0,
            'time_search' => 0,
            'time_request' => 0,
        ]);
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
