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
        $this->middleware('auth');
    }
    
    public function index()
    {

        return response()->json([
            'success'   => true,
            'data' => [
                url("/api/v1/solve/first"),
                url("/api/v1/solve/two"),
                url("/api/v1/solve/fjmaxmin"),
                url("/api/v1/solve/normalization"),
                url("/api/v1/solve/weighted"),
                url("/api/v1/solve/srmaxmin"),
                url("/api/v1/solve/q"),
                url("/api/v1/solve/v05"),
                url("/api/v1/solve/v06"),
                url("/api/v1/solve/v07")
            ]
        ]);
        
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
        $this->weighted = $this->_weighting();
        $this->SRMaxMin = $this->_SRMaxMin();
        $this->q = $this->_getQ();
        $this->v05 = $this->_v05();
        $this->v06 = $this->_v06();
        $this->v07 = $this->_v07();
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
        }elseif($step == 'weighted'){
            return response()->json([
                'success'   => true,
                'data' => $this->weighted
            ]);
        }elseif($step == 'srmaxmin'){
            return response()->json([
                'success'   => true,
                'data' => $this->SRMaxMin
            ]);
        }elseif($step == 'q'){
            return response()->json([
                'success'   => true,
                'data' => $this->q
            ]);
        }elseif($step == 'v05'){
            return response()->json([
                'success'   => true,
                'data' => $this->v05
            ]);
        }elseif($step == 'v06'){
            return response()->json([
                'success'   => true,
                'data' => $this->v06
            ]);
        }elseif($step == 'v07'){
            return response()->json([
                'success'   => true,
                'data' => $this->v07
            ]);
        }
        else{
            return response()->json([
                'success'   => false,
                'message'   => 'not found'
            ],400);
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
                $data = array();
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

    private function _getWeightOfCriteria()
    {
        $data = array();

        $criteria = new Criteria();

        $criteriaID = $criteria::select('weight')->orderByRaw('alias')->get();

        foreach ($criteriaID as $key => $value) {
            array_push($data,$value["weight"]);
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

    private function _weighting()
    {
        $weight = $this->_getWeightOfCriteria();
        $value = array();
        $data = array();
        $Sij =0;
        $Rij =0;
        try{
            for($i = 0; $i < count($this->normalized); $i++){
                for($j = 0; $j < count($this->normalized[$i]["value"]); $j++){

                    $value[$j] = array(
                        'criteria_id'   => $this->normalized[$i]["value"][$j]["criteria_id"],
                        'criteria_alias'    => $this->normalized[$i]["value"][$j]["criteria_alias"],
                        'criteria_category' => $this->normalized[$i]["value"][$j]["criteria_category"],
                        'value' => number_format($this->normalized[$i]["value"][$j]["value"] * ($weight[$j] /100),4)
                    );
                    $Sij += $value[$j]["value"];
                    if($Rij < $value[$j]["value"]) $Rij = $value[$j]["value"];

                }
                $data[$i] = [
                    'alternatif_id'    => $this->normalized[$i]["alternatif_id"],
                    'alternatif_no_induk_dta'   => $this->normalized[$i]["alternatif_no_induk_dta"],
                    'alternatif_name'   =>$this->normalized[$i]["alternatif_name"],
                    'dta_name'  => $this->normalized[$i]["dta_name"],
                    'value' => $value,
                    'Si'   => $this->_getSiRi($value,"S"),
                    'Ri'   => $this->_getSiRi($value,"R")
                ];
                

            }
            
        }catch(QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }

        return $data;
    }

    private function _getSiRi($data,$type)
    {
        $S = 0;
        $R = 0;
        if($type == "S"){
            for($i=0 ; $i < count($data);$i++){
                $S+= $data[$i]["value"];
            }
            return number_format($S,4);
        }elseif($type == "R"){
            for($i = 0; $i < count($data);$i++){
                if($R < $data[$i]["value"]){
                    $R = $data[$i]["value"];
                }
            }

            return number_format($R,4);
        }
    }

    private function _SRMaxMin()
    {
        $tempS = array();
        $tempR = array();
        for($i = 0; $i < count($this->weighted); $i++){
            $tempR[$i] = $this->weighted[$i]["Ri"];
            $tempS[$i] = $this->weighted[$i]["Si"];
        }

        $data = array(
            "S*"  => min($tempS),
            "S-"  => max($tempS),
            "R*"  => min($tempR),
            "R-"  => max($tempR),
        );

        return $data;

    }

    private function _getQ()
    {

        for($i = 0; $i < count($this->weighted); $i++){
            
            $data[$i] = [
                'alternatif_id'    => $this->weighted[$i]["alternatif_id"],
                'alternatif_no_induk_dta'   => $this->weighted[$i]["alternatif_no_induk_dta"],
                'alternatif_name'   =>$this->weighted[$i]["alternatif_name"],
                'dta_name'  => $this->weighted[$i]["dta_name"],
                '0.5' => (float)$this->_calcQi($this->weighted[$i]["Si"],$this->weighted[$i]["Ri"],0.5),
                '0.6' => (float)$this->_calcQi($this->weighted[$i]["Si"],$this->weighted[$i]["Ri"],0.6),
                '0.7' => (float)$this->_calcQi($this->weighted[$i]["Si"],$this->weighted[$i]["Ri"],0.7)
                
            ];
        }

        return $data;
    }

    private function _calcQi($Si,$Ri,$v)
    {
        /*
        |
        |Q i = v [(Si − S* )/(s- - s*)] + (1 − v) [(Ri− R* )/(R- - R*)]
        |
        |*/


        return number_format(((($v*($Si -$this->SRMaxMin["S*"]) / ($this->SRMaxMin["S-"] - $this->SRMaxMin["S*"])) + ((1-$v)*($Ri-$this->SRMaxMin["R*"]) / ($this->SRMaxMin["R-"] - $this->SRMaxMin["R*"])))),4);
    }
    private function _v05()
    {
        $data = array();
        $no = 0;
        $q = $this->q;
        $keys = array_column($q, '0.5');
        array_multisort($keys, SORT_ASC, $q);
        foreach ($q as $key) {
            $no++;
            $data[]=[
                'alternatif_id'    => $key["alternatif_id"],
                'alternatif_no_induk_dta'   => $key["alternatif_no_induk_dta"],
                'alternatif_name'   =>$key["alternatif_name"],
                'dta_name'  => $key["dta_name"],
                'value' => $key["0.5"],
                'rank'  => $no
                
            ];
        }
        return $data;
    }
    private function _v06()
    {
        $data = array();
        $no = 0;
        $q = $this->q;
        $keys = array_column($q, '0.6');
        array_multisort($keys, SORT_ASC, $q);
        foreach ($q as $key) {
            $no++;
            $data[]=[
                'alternatif_id'    => $key["alternatif_id"],
                'alternatif_no_induk_dta'   => $key["alternatif_no_induk_dta"],
                'alternatif_name'   =>$key["alternatif_name"],
                'dta_name'  => $key["dta_name"],
                'value' => $key["0.6"],
                'rank'  => $no
                
            ];
        }
        return $data;
    }
    private function _v07()
    {
        $data = array();
        $no = 0;
        $q = $this->q;
        $keys = array_column($q, '0.7');
        array_multisort($keys, SORT_ASC, $q);
        foreach ($q as $key) {
            $no++;
            $data[]=[
                'alternatif_id'    => $key["alternatif_id"],
                'alternatif_no_induk_dta'   => $key["alternatif_no_induk_dta"],
                'alternatif_name'   =>$key["alternatif_name"],
                'dta_name'  => $key["dta_name"],
                'value' => $key["0.7"],
                'rank'  => $no
                
            ];
        }
        return $data;
    }
    
}
