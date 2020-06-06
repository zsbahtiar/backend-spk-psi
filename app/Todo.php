<?php

namespace App;

// use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
// use Laravel\Lumen\Auth\Authorizable;

class Todo extends Model
{
    protected $table = 'todo'; 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity', 'desecription'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
