<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\Laboran;
use App\Models\Makul;
use App\Models\PeriodeTahun;
use App\Models\Prodi;
use App\Models\Ruangan;
use App\Models\WaktuPerkuliahan;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function getFilterData(){
        $periode = PeriodeTahun::select('periode')->distinct()->get();
        $hari = Jadwal::select('hari_jadwal')->distinct()->get();
        $prodi = Prodi::select('nama_prodi')->distinct()->get();
        $makul = Makul::select('nama_makul')->distinct()->get();
        $jenis_jadwal = Jadwal::select('jenis_jadwal')->distinct()->get();
        $dosen = Dosen::select('nama_dosen')->distinct()->get();
        $laboran = Laboran::select('nama_laboran')->distinct()->get();;
        $ruangan = Ruangan::select('nama_ruangan')->distinct()->get();;
        return [
            "periode"=>$periode,
            "hari"=>$hari,
            "prodi"=>$prodi,
            "makul"=>$makul,
            "jenis_jadwal"=>$jenis_jadwal,
            "dosen"=>$dosen,
            "laboran"=>$laboran,
            "ruangan"=>$ruangan
        ];
    }
    public function getInputData(){
        $periode = PeriodeTahun::select('id','periode')->distinct()->get();
        $prodi = Prodi::select('id','nama_prodi')->distinct()->get();
        $makul = Makul::select('id','nama_makul')->distinct()->get();
        $dosen = Dosen::select('id', 'nama_dosen')->distinct()->get();
        $laboran = Laboran::select('id', 'nama_laboran')->distinct()->get();;
        $ruangan = Ruangan::select('id', 'kode_ruangan')->distinct()->get();;
        $waktu = WaktuPerkuliahan::all();
        return [
            "periode"=>$periode,
            "prodi"=>$prodi,
            "makul"=>$makul,
            "dosen"=>$dosen,
            "laboran"=>$laboran,
            "ruangan"=>$ruangan,
            "waktu"=>$waktu
        ];
    }
    //
}
