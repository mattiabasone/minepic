<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class AccountsNotFound
 * @package App\Database
 *
 * @property string request
 * @property int time
 */
class AccountsNotFound extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'accounts_not_found';

    /**
     * Primary key
     *
     * @var string
     */
    protected $primaryKey = 'request';

    /**
     * No primary key
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;
}