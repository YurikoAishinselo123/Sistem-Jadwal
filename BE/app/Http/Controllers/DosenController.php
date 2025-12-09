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
            'kode_dosen' => 'required',
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
        $dosen1_data = $dosen->jadwal1()->get();
        $dosen2_data = $dosen->jadwal2()->get();//
        $total_data = $dosen1_data->merge($dosen2_data);
        return $total_data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dosen $dosen)
    {
        $fields=$request->validate([
            'kode_dosen' => 'required',
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
        if($dosen->jadwal1()->exists()|$dosen->jadwal2()->exists()){
            return response("Data dosen tidak bisa dihapus karena terdapat di data jadwal", 422);
        }else{
            $dosen->delete();//
            return[
                "messsage"=>"Data dosen berhasil dihapus"
            ];
        }
        
    }
}
