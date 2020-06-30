<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Alternatif;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class AlternatifController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->alternatif = new Alternatif();
    }

    //

    public function index()
    {
        try{
            return response()->json([
                'success' => true,
                'data' => $this->alternatif->get_all()],200);
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    public function show($id)
    {
        try{
            $alternatif = $this->alternatif->get_select($id);
            $isExists = count($alternatif) == 1;
            
            if($isExists){
                return response()->json([
                    'success' => true,
                    'data' => $alternatif
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
        $no_induk_dta = $request->input('no_induk_dta');
        $name = $request->input('name');
        $nik = $request->input('nik');
        $gender = $request->input('gender');
        if($name == null || $dta_id == null || $no_induk_dta == null || $nik == null || $gender == null){
            return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
        }else{
                try{
                    $data = array(
                        'dta_id' => $dta_id,
                        'no_induk_dta' => $no_induk_dta,
                        'name' => $name,
                        'nik' => $nik,
                        'gender' => $gender
                    );
                    $save = $this->alternatif->new($data);  

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
        $alternatif = $this->alternatif::where('id',$id)->limit(1)->get();
        $isExists = count($alternatif) == 1;
        
        $dta_id = $request->input('dta_id');
        $no_induk_dta = $request->input('no_induk_dta');
        $name = $request->input('name');
        $nik = $request->input('nik');
        $gender = $request->input('gender');

        if($isExists){
            if($name == null || $dta_id == null || $no_induk_dta == null || $nik == null || $gender == null){
                return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
            }else{
                $data = array(
                        'dta_id' => $dta_id,
                        'no_induk_dta' => $no_induk_dta,
                        'name' => $name,
                        'nik' => $nik,
                        'gender' => $gender
                    );
                try{
                    $update = $this->alternatif::where('id',$id)->update($data);
                    
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
            $data = $this->alternatif::where('id', $id)->first();
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
            case 23503://master nof found
                $data = [
                    'success'   => false,
                    'status'    => 'data master not found'
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
