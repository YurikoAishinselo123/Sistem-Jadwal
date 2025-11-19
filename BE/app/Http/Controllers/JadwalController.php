<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Http\Requests\StoreJadwalRequest;
use App\Http\Requests\UpdateJadwalRequest;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Jadwal::filter()->get();//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'hari_jadwal' => 'required',
            'waktu_mulai'=> 'required',
            'sesi'=> 'required',
            'tahun_ajaran'=> 'required',
            'id_prodi'=> 'required',
            'id_makul'=> 'required',
            'id_dosen'=> 'required',
            'id_laboran'=> 'required',
            'id_ruangan'=> 'required'            
        ]);//

        $jadwal = Jadwal::create($fields);
        return ['jadwal' => $jadwal];
    }

    /**
     * Display the specified resource.
     */
    public function show(Jadwal $jadwal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        $fields = $request->validate(([
            'hari_jadwal' => 'required',
            'waktu_mulai'=> 'required',
            'sesi'=> 'required',
            'tahun_ajaran'=> 'required',
            'id_prodi'=> 'required',
            'id_makul'=> 'required',
            'id_dosen'=> 'required',
            'id_laboran'=> 'required',
            'id_ruangan'=> 'required'    
        ]));
        $jadwal->update(($fields));
        return ['jadwal' => $jadwal];
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();//  
        return ['message' => 'message deleted'];
        //
    }
}
