<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\CriteriaBenefitValues;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CriteriaBenefitValuesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->CriteriaBenefit = new CriteriaBenefitValues();
    }

    //

    public function index()
    {
        try{
            return response()->json([
                'success' => true,
                'data' => $this->CriteriaBenefit::all()],200);
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    public function show($id)
    {

        try{
            $CriBen = $this->CriteriaBenefit::where('id',$id)->get();
            $isExists = count($CriBen) == 1;
            if($isExists){
                return response()->json([
                    'success' => true,
                    'data' => $CriBen
                ],200);
            }
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    public function store(Request $request)
    {

        $name = $request->input('name');
        $value = $request->input('value');
        if($name == null || $value == null){
            return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
        }else{
                try{
                    $data = array(
                        'name' => $name,
                        'value' => $value
                    );
                    $save = $this->CriteriaBenefit->new($data);  

                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ],201);  
                }catch (QueryException $e){
                    $errorCode = $e->errorInfo[0];
                    return response()->json($this->_errorMessage($errorCode));
                }    
            }
        
        
    }
    public function update($id,Request $request)
    {
        $criteria = $this->CriteriaBenefit::where('id',$id)->limit(1)->get();
        $isExists = count($criteria) == 1;
        
        $name = $request->input('name');
        $value = $request->input('value');

        if($isExists){
            if($name == null || $value == null){
                return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
            }else{
                $data = array(
                        'name' => $name,
                        'value' => $value
                    );
                try{
                    $update = $this->CriteriaBenefit::where('id',$id)->update($data);
                    
                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ],200);

                }catch (QueryException $e){
                    $errorCode = $e->errorInfo[0];
                    return response()->json($this->_errorMessage($errorCode));
                }
                
            }

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data yang dicari tidak ada',
            ], 404);
        }

        
    }

    public function remove($id)
    {
        try{
            $data = $this->CriteriaBenefit::where('id', $id)->first();
            $data->delete();
            return response()->json([
                'success' => true,
                'data' => $data
            ],200);
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
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
}
