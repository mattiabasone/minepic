<?php

declare(strict_types=1);

namespace Minepic\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Minepic\Models\Account;

class AccountRepository
{
    /**
     * @param array $filters
     *
     * @return Builder
     */
    public function filterQuery(array $filters = []): Builder
    {
        $query = Account::query();
        if (\array_key_exists('term', $filters)) {
            $query->where('username', 'LIKE', '%'.$filters['term'].'%');
        }

        return $query;
    }

    /**
     * Find account using UUID.
     *
     * @param string $uuid
     * @param array  $columns
     *
     * @return Account
     */
    public function findByUuid(string $uuid, $columns = ['*']): ?Account
    {
        return Account::whereUuid($uuid)
            ->select($columns)
            ->first();
    }

    /**
     * @param string $username
     * @param array  $columns
     *
     * @return Account
     */
    public function findByUsername(string $username, $columns = ['*']): ?Account
    {
        return Account::whereUsername($username)
            ->select($columns)
            ->first();
    }

    /**
     * Last updated username.
     *
     * @param string $uuid
     * @param array  $columns
     *
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function findLastUpdatedByUsername(string $uuid, $columns = ['*'])
    {
        return Account::query()
            ->select($columns)
            ->whereUsername($uuid)
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    /**
     * @param array    $filters
     * @param int|null $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param int|null $page
     *
     * @return LengthAwarePaginator
     */
    public function filterPaginate(
        array $filters,
        $perPage = null,
        $columns = ['*'],
        $pageName = 'page',
        $page = null
    ): LengthAwarePaginator {
        return $this->filterQuery($filters)
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
