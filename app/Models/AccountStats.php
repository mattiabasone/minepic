<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'time_search'
    ];

    /**
     * Get most wanted users.
     *
     * @return mixed
     */
    public static function getMostWanted()
    {
        $default_uuid = env('DEFAULT_UUID');

        return DB::select(
            "SELECT a.`uuid`, a.`username`, s.`count_request`
             FROM (
              SELECT `uuid`, `count_request` FROM `accounts_stats`
              WHERE `uuid` != '{$default_uuid}'
              ORDER BY `count_request` DESC
              LIMIT 14
            ) s
            INNER JOIN `accounts` AS a USING(`uuid`)
            ORDER BY s.`count_request` DESC"
        );
    }

    /**
     * Get last users.
     *
     * @return mixed
     */
    public static function getLastUsers()
    {
        return DB::select(
            'SELECT a.`uuid`, a.`username`, s.`count_request`
              FROM (
                SELECT `uuid`, `count_request` FROM `accounts_stats`
                ORDER BY `time_request` DESC
                LIMIT 9
              ) s
            INNER JOIN `accounts` a USING(`uuid`)
            ORDER BY s.`count_request` DESC'
        );
    }
}
