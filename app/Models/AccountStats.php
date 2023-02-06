<?php

declare(strict_types=1);

namespace Minepic\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AccountsStats.
 *
 * @property string         $uuid
 * @property int            $count_request
 * @property null|\Carbon\Carbon $request_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccountStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUuid($value)
 * @mixin Eloquent
 */
class AccountStats extends Model
{
    /**
     * No primary key.
     */
    public $incrementing = false;

    public $timestamps = false;

    public $dates = [
        'request_at',
    ];

    public $casts = [
        'uuid' => 'string',
        'count_request' => 'int',
        'request_at' => 'datetime',
    ];
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'accounts_stats';

    /**
     * Primary key.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'uuid',
        'count_request',
        'request_at',
    ];

    /**
     * Get most wanted users.
     */
    public static function getMostWanted(int $limit = 14): array
    {
        return \DB::select(
            "SELECT a.`uuid`, a.`username`, s.`count_request`
             FROM (
              SELECT `uuid`, `count_request` FROM `accounts_stats`
              ORDER BY `count_request` DESC
              LIMIT {$limit}
            ) s
            INNER JOIN `accounts` AS a USING(`uuid`)
            ORDER BY s.`count_request` DESC"
        );
    }

    /**
     * Get last users.
     */
    public static function getLastUsers(int $limit = 9): array
    {
        return \DB::select(
            "SELECT a.`uuid`, a.`username`, s.`count_request`
              FROM (
                SELECT `uuid`, `count_request` FROM `accounts_stats`
                ORDER BY `request_at` DESC
                LIMIT {$limit}
              ) s
            INNER JOIN `accounts` a USING(`uuid`)
            ORDER BY s.`count_request` DESC"
        );
    }
}
