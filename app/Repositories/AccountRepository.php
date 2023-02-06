<?php

declare(strict_types=1);

namespace Minepic\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Minepic\Models\Account;

class AccountRepository
{
    /**
     * @return Builder<Account>
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
     */
    public function findLastUpdatedByUsername(string $uuid): ?Account
    {
        return Account::query()
            ->select()
            ->whereUsername($uuid)
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    /**
     * @return LengthAwarePaginator<Account>
     */
    public function filterPaginate(
        array $filters,
        ?int $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        ?int $page = null
    ): LengthAwarePaginator {
        return $this->filterQuery($filters)
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
