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
     * Last updated username.
     *
     * @param string $uuid
     * @param array  $columns
     *
     * @return null|Account
     */
    public function findLastUpdatedByUsername(string $uuid, $columns = ['*']): ?Account
    {
        return Account::query()
            ->select($columns)
            ->whereUsername($uuid)
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    /**
     * @param array    $filters
     * @param null|int $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param null|int $page
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
