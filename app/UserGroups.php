<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserGroups extends Model
{
    protected $table = 'user_groups';

    /**
     * @var array
     */
    protected $filable = [
        'id',
        'user_id',
        'group_id',
        'active',
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
