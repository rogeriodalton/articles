<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessGroups extends Model
{
    protected $table = 'access_groups';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'name',
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
