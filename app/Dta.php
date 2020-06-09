<?php

namespace App;

// use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
// use Laravel\Lumen\Auth\Authorizable;

class Dta extends Model
{
    // use Authenticatable, Authorizable;

    protected $table = 'dta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_statistik','name','address','headmaster',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function new($data){
        $this->no_statistik = $data["no_statistik"];
        $this->name = $data["name"];
        $this->address = $data["address"];
        $this->headmaster = $data["headmaster"];

        $this->save();
    }
}
