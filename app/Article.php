<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'code',
        'name',
        'price',
        'quantity'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'fname',
        'created_at',
        'updated_at',
    ];

     /**
     * define phonetics attribute
     */
    public function setFnameAttribute($value)
    {
        $this->attributes['fname'] = phonetics($value);
    }


}
