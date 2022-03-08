<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountOrders extends Model
{
    protected $table = 'discount_orders';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'name',
        'value_min',
        'value_max',
        'discount_percent',
        'active',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
