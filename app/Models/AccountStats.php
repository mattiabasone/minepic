<?php

declare(strict_types=1);

namespace App\Models;

use App\Minecraft\MinecraftDefaults;
use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AccountsStats.
 *
 * @property string $uuid
 * @property int    $count_request
 * @property int    $count_search
 * @property int    $time_request
 * @property int    $time_search
 */
class AccountStats extends Model
{
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
     * No primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'uuid',
        'count_request',
        'count_search',
        'time_request',
        'time_search',
    ];

    /**
     * Get most wanted users.
     *
     * @param int $limit
     *
     * @return mixed
     */
    public static function getMostWanted(int $limit = 14)
    {
        $defaultUuid = MinecraftDefaults::UUID;

        return DB::select(
            "SELECT a.`uuid`, a.`username`, s.`count_request`
             FROM (
              SELECT `uuid`, `count_request` FROM `accounts_stats`
              WHERE `uuid` != '{$defaultUuid}'
              ORDER BY `count_request` DESC
              LIMIT {$limit}
            ) s
            INNER JOIN `accounts` AS a USING(`uuid`)
            ORDER BY s.`count_request` DESC"
        );
    }

    /**
     * Get last users.
     *
     * @param int $limit
     *
     * @return mixed
     */
    public static function getLastUsers(int $limit = 9)
    {
        return DB::select(
            'SELECT a.`uuid`, a.`username`, s.`count_request`
              FROM (
                SELECT `uuid`, `count_request` FROM `accounts_stats`
                ORDER BY `time_request` DESC
                LIMIT '.$limit.'
              ) s
            INNER JOIN `accounts` a USING(`uuid`)
            ORDER BY s.`count_request` DESC'
        );
    }
}
