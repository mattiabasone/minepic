<?php
namespace App\Database;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Accounts
 * @package App\Database
 *
 * Table fields
 *
 * @property int $id
 * @property string $uuid
 * @property string $username
 * @property string $skin_md5
 * @property int $fail_count
 * @property int $updated
 * @property string $skin
 * @property string $cape
 */
class Accounts extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'account';

    /**
     * @var bool
     */
    public $timestamps = false;
}