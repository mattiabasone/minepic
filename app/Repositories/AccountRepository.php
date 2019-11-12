<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use Czim\Repository\BaseRepository;

/**
 * Class AccountRepository.
 */
class AccountRepository extends BaseRepository
{
    public function model(): string
    {
        return Account::class;
    }

    /**
     * Find account using UUID
     *
     * @param string $uuid
     * @param array $columns
     * @return Account
     */
    public function findByUuid(string $uuid, $columns = ['*']): Account
    {
        return $this->findBy('uuid', $uuid, $columns);
    }

    /**
     * @param string $uuid
     * @param array $columns
     * @return Account
     */
    public function findByUsername(string $uuid, $columns = ['*']): Account
    {
        return $this->findBy('username', $uuid, $columns);
    }

    /**
     * Last updated username.
     *
     * @param string $uuid
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function findLastUpdatedByUsername(string $uuid, $columns = ['*'])
    {
        return $this->query()
            ->select($columns)
            ->where('username', '=', $uuid)
            ->orderBy('updated_at', 'desc')
            ->first();
    }
}
