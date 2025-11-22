<?php

namespace App\Http\Controllers;

use App\Models\Laboran;
use App\Http\Requests\StoreLaboranRequest;
use App\Http\Requests\UpdateLaboranRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LaboranController extends Controller implements HasMiddleware
{
    public static function middleware(){
        return[
            new Middleware('auth:sanctum', except:['index', 'show'])
        ];
    }    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Laboran::all();//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'nama_laboran' => 'required'        
        ]);//

        $laboran = Laboran::create($fields);
        return ['laboran' => $laboran];//
    }

    /**
     * Display the specified resource.
     */
    public function show(Laboran $laboran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laboran $laboran)
    {
        $fields=$request->validate([
            'nama_laboran' => 'required'        
        ]);//

        $laboran->update($fields);
        return ['laboran' => $laboran];//        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laboran $laboran)
    {
        $laboran->delete();//
    }
}
