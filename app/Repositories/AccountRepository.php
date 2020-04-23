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
    /**
     * @return string
     */
    public function model(): string
    {
        return Account::class;
    }

    /**
     * @param array $filters
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterQuery(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->query();
        if (\array_key_exists('term', $filters)) {
            $query->where('username', 'LIKE', $filters['term'].'%');
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
        return $this->findBy('uuid', $uuid, $columns);
    }

    /**
     * @param string $uuid
     * @param array  $columns
     *
     * @return Account
     */
    public function findByUsername(string $uuid, $columns = ['*']): ?Account
    {
        return $this->findBy('username', $uuid, $columns);
    }

    /**
     * Last updated username.
     *
     * @param string $uuid
     * @param array  $columns
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

    /**
     * @param array  $filters
     * @param int|null   $perPage
     * @param array  $columns
     * @param string $pageName
     * @param int|null   $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filterPaginate(
        array $filters,
        $perPage = null,
        $columns = ['*'],
        $pageName = 'page',
        $page = null
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        return $this->filterQuery($filters)
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
