<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'name',
        'fname',
        'email',
    ];

    /**
     * @var array
     */
    protected $hidden = [
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
