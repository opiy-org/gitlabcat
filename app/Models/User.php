<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class User extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'uid',
        'gitlab_name',
        'api_key',
        'gitlab_id',
        'rights',
        'settings',
    ];

    protected $appends = [
        'is_coder',
        'is_admin'
    ];

    protected $casts = [
        'settings' => 'array'
    ];


    /**
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->rights >= 777;
    }

    /**
     * @return bool
     */
    public function getIsCoderAttribute()
    {
        return ($this->rights > 0 && ($this->api_key !== null) && ($this->gitlab_id !== null));
    }


}
