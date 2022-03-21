<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $table = 'order_items';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'order_id',
        'article_id',
        'units',
        'unit_value',
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

    public function order()
    {
        return $this->hasOne(Order::class , 'order_id', 'id');
    }
}
