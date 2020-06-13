<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Alternatif;
use App\Enumerisation;
use App\ValuesOfAlternatif;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ResultController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->result = new Result();
    }

    //

    public function index()
    {
        
    }

    private function _changeToNum($name)
    {
        $enumerisation = new Enumerisation();
        $changer = $enumerisation::where('name',$name)->get();
        return $changer[0]->value;
    }

    public function solve($step)
    {
        $data = array();
        $alternatif = $this->_getAlternatif();
        if($step == 'first'){
            foreach ($alternatif as $key => $value) {
                $data[$key] = [
                    'alternatif_id'    => $value->id,
                    'alternatif_no_induk_dta'   => $value->no_induk_dta,
                    'alternatif_name'   => $value->name,
                    'dta_name'  => $value->dta_name,
                    'value' => $this->_getValueOf($value->id)
                    
                ];
            }
        
            return response()->json([
                'success' => true,
                'data' => $data
            ],200);
        
        }elseif($step == 'two'){
            foreach ($alternatif as $key => $value) {
                   
                $data[$key] = [
                    'alternatif_id'    => $value->id,
                    'alternatif_no_induk_dta'   => $value->no_induk_dta,
                    'alternatif_name'   => $value->name,
                    'dta_name'  => $value->dta_name,
                    'value' => $this->_getValueOf($value->id,'yes')
                    
                ];
            }
        
            return response()->json([
                'success' => true,
                'data' => $data
            ],200);
        }

        
            
        
    }
    private function _errorMessage($errorCode)
    {
        switch ($errorCode) {
            case 23505://unique
                $data = [
                    'success' => false,
                    'status' => 'already on database'
                ];   
            break;
            case 22001://value limited
                $data = [
                    'success' => false,
                    'status' => 'value name is limited'
                ];                       
            break;
            default:
                $data = [
                    'success' => false,
                    'status' => $errorCode
                ];
            break;      
        }

        return $data;

        
    }

    private function _getAlternatif()
    {
        try{
            $alternatif = new Alternatif();
            return $alternatif->AlternatifToResult();
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    private function _getValueOf($alternatif,$enumerisation=null)
    {
        try{
            $ValueOfAlternatif = new ValuesOfAlternatif();
            $result = $ValueOfAlternatif->ValueToResult($alternatif); 
            if($enumerisation=='yes'){
                $valueAlternatif='';
                foreach ($result as $key => $value) {
                    if(is_numeric($value->value)){
                        $valueAlternatif = (float)$value->value;
                    }else{
                        $valueAlternatif = $this->_changeToNum($value->value);
                    }

                    $data[$key] = [
                        'criteria_id'   => $value->criteria_id,
                        'criteria_alias'    => $value->criteria_alias,
                        'criteria_name' => $value->criteria_name,
                        'criteria_category' => $value->criteria_category,
                        'value_id'  => $value->value_id,
                        'value' => $valueAlternatif,
                        'created_at' => $value->created_at,
                        'updated_at'    => $value->updated_at
                    ];
                }
                return $data;
            }
            return $result;
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    
    
}
