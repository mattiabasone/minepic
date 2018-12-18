<?php

declare(strict_types=1);

namespace App\Database;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class AccountsNameChange.
 *
 * @property int    $id
 * @property string $uuid
 * @property string $prev_name
 * @property string $new_name
 * @property int    $time_change
 */
class AccountsNameChange extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'accounts_name_change';

    /**
     * @var bool
     */
    public $timestamps = false;
}
