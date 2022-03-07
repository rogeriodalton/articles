<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccessGroups extends Model
{
    protected $table = 'user_access_groups';

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
