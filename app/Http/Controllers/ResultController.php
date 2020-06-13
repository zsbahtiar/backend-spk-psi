<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Alternatif;
use App\Enumerisation;
use App\Criteria;
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
        $this->defineMatrix = $this->_convertToMatrix(); 
        $this->FjMaxMin = $this->_FjMaxMin();
        $this->normalized = $this->_normalization();
        if($step == 'first'){
            return response()->json([
                'success' => true,
                'data' => $this->_rawData()
            ],200);
        
        }elseif($step == 'two'){
            return response()->json([
                'success' => true,
                'data' => $this->defineMatrix
            ],200);
        }elseif($step == 'fjmaxmin'){
            return response()->json([
                'success' => true,
                'data' => $this->FjMaxMin
            ],200);
        }elseif($step == 'normalization'){
            return response()->json([
                'success'   => true,
                'data' => $this->normalized
            ]);
        }

        
            
        
    }
    private function _rawData()
    {
        $alternatif = $this->_getAlternatif();
        $data = array();

        foreach ($alternatif as $key => $value) {
            $data[$key] = [
                'alternatif_id'    => $value->id,
                'alternatif_no_induk_dta'   => $value->no_induk_dta,
                'alternatif_name'   => $value->name,
                'dta_name'  => $value->dta_name,
                'value' => $this->_getValueOf($value->id)
                    
            ];
        }

        return $data;
    }
    private function _convertToMatrix()
    {
        $alternatif = $this->_getAlternatif();
        $data = array();

        foreach ($alternatif as $key => $value) {            
            $data[$key] = [
                'alternatif_id'    => $value->id,
                'alternatif_no_induk_dta'   => $value->no_induk_dta,
                'alternatif_name'   => $value->name,
                'dta_name'  => $value->dta_name,
                'value' => $this->_getValueOf($value->id,'yes')
            ];
        }

        return $data;

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

    private function _FjMaxMin(){
        $data = array();
        $value = $this->_getValue();

        for($i = 0; $i < count($value); $i++){
            $data[$i]["max"] = max($value[$i]);
            $data[$i]["min"] = min($value[$i]);
        }

        return $data;

    }

    private function _getValue()
    {
        $data = array();

        for($i = 0; $i < count($this->defineMatrix); $i++){
            for($j = 0; $j < count($this->defineMatrix[$i]["value"]); $j++){
                $data[$j][] = $this->defineMatrix[$i]["value"][$j]["value"];
            }

        }
        
        return $data;
    }

    private function _getCriteria()
    {
        $data = array();

        $criteria = new Criteria();

        $criteriaID = $criteria::select('id')->orderByRaw('alias')->get();

        foreach ($criteriaID as $key => $value) {
            array_push($data,$value["id"]);
        }


        return $data;
    }

    private function _normalization()
    {
        
        $pack = array();
        $value = array();
        $data = array();
        try{
            for($i = 0; $i < count($this->defineMatrix); $i++){
                for($j = 0; $j < count($this->defineMatrix[$i]["value"]); $j++){
                    $pack = array(

                        "category"  => $this->defineMatrix[$i]["value"][$j]["criteria_category"],
                        "fjmax" => $this->FjMaxMin[$j]["max"],
                        "fjmin" => $this->FjMaxMin[$j]["min"],
                        "fij"   => $this->defineMatrix[$i]["value"][$j]["value"]
                    );
                    $value[$j] = array(
                        'criteria_id'   => $this->defineMatrix[$i]["value"][$j]["criteria_id"],
                        'criteria_alias'    => $this->defineMatrix[$i]["value"][$j]["criteria_alias"],
                        'criteria_category' => $pack["category"],
                        'value' => $this->_calcNormalization($pack)
                    );
                }
                $data[$i] = [
                    'alternatif_id'    => $this->defineMatrix[$i]["alternatif_id"],
                    'alternatif_no_induk_dta'   => $this->defineMatrix[$i]["alternatif_no_induk_dta"],
                    'alternatif_name'   =>$this->defineMatrix[$i]["alternatif_name"],
                    'dta_name'  => $this->defineMatrix[$i]["dta_name"],
                    'value' => $value
                ];
                

            }
            return $data;
            
        }catch(QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
        
        
    }
    private function _calcNormalization($data){
        /*
        |
        |---------------------------------
        |Benefit
        |Profit is calling a benefit
        |---------------------------------
        |Benefit = (FjMax - Fij) / (FjMax - Fjmin)
        |
        |
        |---------------------------------
        |Cost
        |
        |Non Profit is Caling Cost
        |---------------------------------
        |Cost = (FjMin - Fij) / (FjMin - Fjmax)
        |
        |
        */
        if($data["category"] != "benefit"){
            return number_format((($data["fjmin"] - $data["fij"]) / ($data["fjmin"] - $data["fjmax"])),4);
        }

        return number_format((($data["fjmax"] - $data["fij"]) / ($data["fjmax"] - $data["fjmin"])),4);

    }

  
    
    
}
