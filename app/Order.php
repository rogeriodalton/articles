<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;

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
        'first_amount_discount',
        'first_amount_add',
        'amount_discount',
        'amount_add',
        'amount_liquid',
        'amount_gross',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    /**
     * define data do pedido no formato YYYY-MM-DD  attribute
     */
    public function setDateAttribute()
    {
        $ym = DateTime::createFromFormat('m-d-Y', date('m-d-Y'))->format("Y-m-d");
        $this->attributes['date'] = "{$ym}";
    }

    /**
     * define code = YYYY-MM-OrderId  attribute
     */
    public function setCodeAttribute()
    {
        $ym = DateTime::createFromFormat('m-d-Y', date('m-d-Y'))->format("Y-m-");
        $this->attributes['code'] = "{$ym}{$this->attributes['id']}";
    }

}
