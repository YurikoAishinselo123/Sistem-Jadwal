<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Http\Requests\StoreProdiRequest;
use App\Http\Requests\UpdateProdiRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProdiController extends Controller implements HasMiddleware
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
        Prodi::all();//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'nama_prodi' => 'required'        
        ]);//

        $prodi = Prodi::create($fields);
        return ['prodi' => $prodi];        //
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Prodi $prodi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prodi $prodi)
    {
        $fields=$request->validate([
            'nama_prodi' => 'required'        
        ]);//

        $prodi->Prodi::update($fields);
        return ['prodi' => $prodi];
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prodi $prodi)
    {
        $prodi->delete();//
    }
}
