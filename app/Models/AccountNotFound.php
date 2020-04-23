<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AccountsNotFound.
 *
 * @property string              $request
 * @property int                 $time
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
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
     * @var array
     */
    protected $fillable = ['request'];
}
