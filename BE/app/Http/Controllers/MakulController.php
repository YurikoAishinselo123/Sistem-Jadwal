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
            'jenis_makul' => 'required',
            'sks_makul' => 'required',
        ]);
        if($request->jenis_makul == 'praktik'){
            $total_sesi = $request->sks_makul *3;
        } else if($request->jenis_makul == 'teori'){
            $total_sesi = $request->sks_makul;
        } else{
            return response('jenis makul tidak valid', 422);
        }
        

        $makul = Makul::create([
            'kode_makul' => $fields['kode_makul'],
            'nama_makul' => $fields['nama_makul'],
            'jenis_makul' => $fields['jenis_makul'],
            'sks_makul' => $fields['sks_makul'],
            'sesi_makul' => $total_sesi
        ]);
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

        $makul->update($fields);
        return ['makul' => $makul];//
        //
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Makul $makul)
    {
        if($makul->jadwal()->exists()){
            return response("Data makul tidak bisa dihapus karena terdapat di data jadwal", 422);
        }else{
            $makul->delete();//
            return[
                "messsage"=>"Data makul berhasil dihapus"
            ];
        }
    }
}
