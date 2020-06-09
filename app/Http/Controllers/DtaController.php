<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Dta;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DtaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->Dta = new Dta();
    }

    //

    public function index()
    {
        try{
            return response()->json([
                'success' => true,
                'data' => $this->Dta::all()],200);
        }catch (QueryException $e){
            $errorCode = $e->errorInfo[0];
            return response()->json($this->_errorMessage($errorCode));
        }
    }
    public function show($id)
    {
        try{
            $dta = $this->Dta::where('id',$id)->get();
            $isExists = count($dta) == 1;
            if($isExists){
                return response()->json([
                    'success' => true,
                    'data' => $dta
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

        $no_statistik = $request->input('no_statistik');
        $name = $request->input('name');
        $address = $request->input('address');
        $headmaster = $request->input('headmaster');
        if($name == null || $no_statistik == null || $address == null || $headmaster == null){
            return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
        }else{
                try{
                    $data = array(
                        'no_statistik' => $no_statistik,
                        'name' => $name,
                        'address' => $address,
                        'headmaster' => $headmaster,
                    );
                    $save = $this->Dta->new($data);  

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
        $Dta = $this->Dta::where('id',$id)->limit(1)->get();
        $isExists = count($Dta) == 1;
        
        $no_statistik = $request->input('no_statistik');
        $name = $request->input('name');
        $address = $request->input('address');
        $headmaster = $request->input('headmaster');

        if($isExists){
            if($name == null || $no_statistik == null || $address == null || $headmaster == null){
                return response()->json([
                    'success' => false,
                    'message' => 'One of the required attributes were empty',
                ], 400);
            }else{
                $data = array(
                    'no_statistik' => $no_statistik,
                    'name' => $name,
                    'address' => $address,
                    'headmaster' => $headmaster
                );
                try{
                    $update = $this->Dta::where('id',$id)->update($data);
                    
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
            $data = $this->Dta::where('id', $id)->first();
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
