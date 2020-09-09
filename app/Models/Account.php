<?php

declare(strict_types=1);

namespace Minepic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUsername($value)
 * @mixin \Eloquent
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
     * @return BelongsTo
     */
    public function stats(): BelongsTo
    {
        return $this->belongsTo(AccountStats::class, 'uuid', 'uuid');
    }
}
