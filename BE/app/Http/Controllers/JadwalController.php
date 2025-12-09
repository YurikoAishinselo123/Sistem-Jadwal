<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Http\Requests\StoreJadwalRequest;
use App\Http\Requests\UpdateJadwalRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Validator;

class JadwalController extends Controller implements HasMiddleware
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
        $jadwal = Jadwal::select('jadwals.id','jadwals.hari_jadwal', 'prodis.nama_prodi',
        'makuls.nama_makul', 'dosens1.nama_dosen as nama_dosen_1',
        'dosens2.nama_dosen as nama_dosen_2', 'laborans.nama_laboran', 
        'ruangans.kode_ruangan', 'jadwals.kelas', 'jadwals.status',
        'jadwals.waktu_mulai','jadwals.waktu_selesai')
        ->join('prodis','jadwals.prodi_id','=','prodis.id')
        ->join('makuls','jadwals.makul_id','=','makuls.id')
        ->join('dosens as dosens1','jadwals.dosen_1','=','dosens1.id')
        ->join('dosens as dosens2','jadwals.dosen_2','=','dosens2.id')
        ->join('ruangans','jadwals.ruangan_id','=','ruangans.id')
        ->join('laborans','jadwals.laboran_id','=','laborans.id')
        ->get();
        return $jadwal;//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJadwalRequest $request)
    {
        $fields=$request->validated();
        $jadwal = Jadwal::create($fields);
        return ['jadwal' => $jadwal];
    }

    /**
     * Display the specified resource.
     */
    public function show(Jadwal $jadwal)
    {
        $output = $jadwal->select('jadwals.*', 'prodis.nama_prodi',
        'makuls.nama_makul', 'dosens1.nama_dosen as nama_dosen_1',
        'dosens2.nama_dosen as nama_dosen_2', 'laborans.nama_laboran', 
        'ruangans.kode_ruangan','periode.periode')
        ->join('prodis','jadwals.prodi_id','=','prodis.id')
        ->join('makuls','jadwals.makul_id','=','makuls.id')
        ->join('dosens as dosens1','jadwals.dosen_1','=','dosens1.id')
        ->join('dosens as dosens2','jadwals.dosen_2','=','dosens2.id')
        ->join('ruangans','jadwals.ruangan_id','=','ruangans.id')
        ->join('laborans','jadwals.laboran_id','=','laborans.id')
        ->join('periode_tahuns as periode','jadwals.periode_tahun_id','=','periode.id')
        ->get()->where('id',$jadwal->id);
        return $output;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJadwalRequest $request, Jadwal $jadwal)
    {
        $fields = $request->validated();
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
