<?php

namespace App;

// use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\DB;
// use Laravel\Lumen\Auth\Authorizable;

class Enumerisation extends Model// implements AuthenticatableContract, AuthorizableContract
{
    protected $table = 'enumerisation';
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','value',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        '',
    ];

    public function new($data)
    {
        $this->name = $data["name"];
        $this->value = $data["value"];

        $this->save();
    }
}
