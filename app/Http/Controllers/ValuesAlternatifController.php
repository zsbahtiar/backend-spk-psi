<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\ValuesOfAlternatif;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;


class ValuesAlternatifController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->value = new ValuesOfAlternatif();
    }

    //

    public function index()
    {
        try{
            return response()->json([
                'success' => true,
                'data' => $this->value->get_all()],200);
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    public function show($id)
    {
        try{
            $values = $this->value->get_select($id);
            $isExists = count($values) == 1;
            if($isExists){ 
                return response()->json([
                    'success' => true,
                    'data' => $values,
                ],200);
            }
            return response()->json([
                        'success' => true,
                        'message' => 'not found'
                    ],200);
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    public function store(Request $request)
    {

        $criteria_id = $request->input('criteria_id');
        $alternatif_id = $request->input('alternatif_id');
        $value = $request->input('value');

        if($criteria_id == null || $alternatif_id == null || $value == null){
            return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
        }else{
                try{
                    $data = array(
                        'criteria_id' => $criteria_id,
                        'alternatif_id' => $alternatif_id,
                        'value' => $value
                    );
                    $save = $this->value->new($data);  

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
        $criteria = $this->value::where('id',$id)->limit(1)->get();
        $isExists = count($criteria) == 1;
        
        $criteria_id = $request->input('criteria_id');
        $alternatif_id = $request->input('alternatif_id');
        $value = $request->input('value');

        if($isExists){
            if($criteria_id == null || $alternatif_id == null || $value == null){
                return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
            }else{
                $data = array(
                        'criteria_id' => $criteria_id,
                        'alternatif_id' => $alternatif_id,
                        'value' => $value
                    );
                try{
                    $update = $this->value::where('id',$id)->update($data);
                    
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
            $data = $this->value::where('id', $id)->first();
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
