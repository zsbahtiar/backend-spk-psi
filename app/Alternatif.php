<?php

namespace App;

// use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// use Laravel\Lumen\Auth\Authorizable;

class Alternatif extends Model
{
    // use Authenticatable, Authorizable;

    protected $table = 'alternatif';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dta_id','no_induk_dta','nik','name','gender',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function new($data){
        $this->dta_id = $data["dta_id"];
        $this->no_induk_dta = $data["no_induk_dta"];
        $this->nik = $data["nik"];
        $this->name = $data["name"];
        $this->gender = $data["gender"];
        
        $this->save();
    }
    public function get_select($id){
        return DB::table('alternatif')
            ->join('dta', 'alternatif.dta_id', '=', 'dta.id')
            ->select('alternatif.*', 'dta.name as nama_dta', 'dta.headmaster')
            ->where('alternatif.id', $id)->limit(1)->get();
    }
    public function get_all(){
        return DB::table('alternatif')
            ->join('dta', 'alternatif.dta_id', '=', 'dta.id')
            ->select('alternatif.*', 'dta.name as nama_dta', 'dta.headmaster')
            ->get();   
    }
}
