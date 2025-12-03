<?php

namespace App\Http\Controllers;

use App\Models\Makul;
use App\Http\Requests\StoreMakulRequest;
use App\Http\Requests\UpdateMakulRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MakulController extends Controller implements HasMiddleware
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
        return Makul::all();//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'kode_makul' => 'required',
            'nama_makul' => 'required',
        ]);//

        $makul = Makul::create($fields);
        return ['makul' => $makul];//
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Makul $makul)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Makul $makul)
    {
        $fields=$request->validate([
            'kode_makul' => 'required',
            'nama_makul' => 'required'
        ]);//

        $makul->Makul::update($fields);
        return ['makul' => $makul];//
        //
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Makul $makul)
    {
        $makul->delete();//
    }
}
