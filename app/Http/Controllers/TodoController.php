<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    public function index()
    {
       
        return response()->json(Todo::all());
    }
    public function show($id)
    {
        return response()->json(Todo::find($id));
    }
    public function create(Request $request)
    {
        $data = new Todo();
        $data->activity = $request->input('activity');
        $data->description = $request->input('description');
        $data->save();
        
        return response()->json($data,201);
    }
}
