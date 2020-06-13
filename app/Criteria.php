<?php

namespace App;

// use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
// use Laravel\Lumen\Auth\Authorizable;

class Criteria extends Model// implements AuthenticatableContract, AuthorizableContract
{
    protected $table = 'criterias';
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','alias','category','weight',
    ];

    /**
     * The attributes excluded from the models JSON form
     *
     * @var array
     */
    protected $hidden = [
        '',
    ];

    public function new($data)
    {
        $this->name = $data["name"];
        $this->alias = $data["alias"];
        $this->category = $data["category"];
        $this->weight = $data["weight"];

        $this->save();
    }
}
