<?php

namespace CodeFin\Models;

use HipsterJazzbo\Landlord\BelongsToTenants;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Client.
 *
 * @package namespace CodeFin\Models;
 */
class BillPay extends Model implements Transformable, BillRepeatTypeInterface
{
    use TransformableTrait;
    use BelongsToTenants;
    use BillTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date_due',
        'name',
        'value',
        'done',
        'bank_account_id',
        'category_id'
    ];

}