<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    protected $table = 'discount_rules';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'articles_id',
        'units_min',
        'units_max',
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
