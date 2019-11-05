<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Accounts.
 *
 * @property int    $id
 * @property string $uuid
 * @property string $username
 * @property int    $fail_count
 * @property string $skin
 * @property string $cape
 * @property string $created_at
 * @property string $updated_at
 */
class Account extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * @var bool
     */
    public $timestamps = true;
}
