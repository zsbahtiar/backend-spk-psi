<?php

namespace App;

// use Illuminate\Auth\Authenticatable;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// use Laravel\Lumen\Auth\Authorizable;

class ValuesOfCriteria extends Model// implements AuthenticatableContract, AuthorizableContract
{
    protected $table = 'values_of_criteria';
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'criteria_id','alternatif_id','value',
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
        $this->criteria_id = $data["criteria_id"];
        $this->alternatif_id = $data["alternatif_id"];
        $this->value = $data["value"];

        $this->save();
    }
    public function get_select($id){
        return DB::table('values_of_criteria')
            ->join('criterias', 'values_of_criteria.criteria_id', '=', 'criterias.id')
            ->join('alternatif', 'values_of_criteria.alternatif_id', '=', 'alternatif.id')
            ->join('dta', 'alternatif.dta_id', '=', 'dta.id')
            ->select('values_of_criteria.*', 'criterias.name as name_criteria','criterias.category','alternatif.name as alternatif_name','dta.name as dta_name')
            ->where('values_of_criteria.id', $id)->limit(1)->get();
    }
    public function get_all(){
        return DB::table('values_of_criteria')
            ->join('criterias', 'values_of_criteria.criteria_id', '=', 'criterias.id')
            ->join('alternatif', 'values_of_criteria.alternatif_id', '=', 'alternatif.id')
            ->join('dta', 'alternatif.dta_id', '=', 'dta.id')
            ->select('values_of_criteria.*', 'criterias.name as name_criteria','criterias.category','alternatif.name as alternatif_name','dta.name as dta_name')
            ->get();
    }
}
