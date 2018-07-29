<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'expiration',
        'project_id'
    ];


    public function project()
    {
        return $this->belongsTo(
            Project::class,
            'project_id',
            'id'
        );
    }


}
