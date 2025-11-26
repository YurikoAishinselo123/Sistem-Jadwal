<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Http\Requests\StoreDosenRequest;
use App\Http\Requests\UpdateDosenRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DosenController extends Controller implements HasMiddleware
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
        return Dosen::all();//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'nama_dosen' => 'required'        
        ]);//

        $dosen = Dosen::create($fields);
        return ['dosen' => $dosen];        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dosen $dosen)
    {
        return $dosen->jadwal()->all();//
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dosen $dosen)
    {
        $fields=$request->validate([
            'nama_dosen' => 'required'        
        ]);//

        $dosen->update($fields);
        return ['dosen' => $dosen];              
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dosen $dosen)
    {
        $dosen->delete();//
    }
}
