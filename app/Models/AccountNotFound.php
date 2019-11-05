<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class AccountsNotFound.
 *
 * @property string request
 * @property int time
 */
class AccountNotFound extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'accounts_not_found';

    /**
     * Primary key.
     *
     * @var string
     */
    protected $primaryKey = 'request';

    /**
     * No primary key autoincrement.
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
    protected $fillable = ['request'];
}
