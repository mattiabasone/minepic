<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Accounts.
 *
 * @property int            $id
 * @property string         $uuid
 * @property string         $username
 * @property int            $fail_count
 * @property string         $skin
 * @property string         $cape
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property AccountStats   $stats
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
     * @var array
     */
    protected $fillable = [
        'uuid',
        'username',
        'fail_count',
        'skin',
        'cape',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stats(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AccountStats::class, 'uuid', 'uuid');
    }
}
