<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Operator;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class OperatorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->operator = new Operator();
    }

    //

    public function index()
    {
        try{
            return response()->json([
                'success' => true,
                'data' => $this->operator::all()],200);
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    public function show($id)
    {
        try{
            $operator = $this->operator::where('id',$id)->get();
            $isExists = count($operator) == 1;
            if($isExists){
                return response()->json([
                    'success' => true,
                    'data' => $operator
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

        $dta_id = $request->input('dta_id');
        $name = $request->input('name');
        $gender = $request->input('gender');
        $email = $request->input('email');
        if($name == null || $dta_id == null || $gender == null || $email == null){
            return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
        }else{
                try{
                    $data = array(
                        'dta_id' => $dta_id,
                        'name' => $name,
                        'gender' => $gender,
                        'email' => $email,
                    );
                    $save = $this->operator->new($data);  

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
        $operator = $this->operator::where('id',$id)->limit(1)->get();
        $isExists = count($operator) == 1;
        
        $dta_id = $request->input('dta_id');
        $name = $request->input('name');
        $gender = $request->input('gender');
        $email = $request->input('email');

        if($isExists){

            if($name == null || $dta_id == null || $gender == null || $email == null){
                    return response()->json([
                        'success' => false,
                        'message' => 'One of the required attributes were empty',
                    ], 400);
                }else{
                    $data = array(
                            'dta_id' => $dta_id,
                            'name' => $name,
                            'gender' => $gender,
                            'email' => $email,
                        );
                    try{
                        $update = $this->operator::where('id',$id)->update($data);
                        
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
            $data = $this->operator::where('id', $id)->first();
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
