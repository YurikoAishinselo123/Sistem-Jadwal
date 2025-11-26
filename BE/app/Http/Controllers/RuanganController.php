<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RuanganController extends Controller implements HasMiddleware
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
        Ruangan::all();//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'nama_ruangan'=>'required',
            'jenis_ruangan'=>'required'
        ]);//
        $ruangan = Ruangan::create($fields);
        return ['ruangan' => $ruangan];
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruangan $ruangan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruangan $ruangan)
    {
        $fields = $request->validate([
            'nama_ruangan'=>'required',
            'jenis_ruangan'=>'required'
        ]);//
        $ruangan->Ruangan::update($fields);
        return ['ruangan' => $ruangan];
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();//
    }
}
