<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'client_id',
        'user_id',
        'code',
        'date',
        'amount_liquid',
        'amount_discount',
        'amount_add',
        'amount_gross',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
