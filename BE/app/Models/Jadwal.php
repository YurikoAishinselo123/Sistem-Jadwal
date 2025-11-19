<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    /** @use HasFactory<\Database\Factories\JadwalFactory> */
    use HasFactory, Filterable;

    protected $fillable = [
        'hari_jadwal',
        'waktu_mulai',
        'sesi',
        'tahun_ajaran',
        'id_prodi',
        'id_makul',
        'id_dosen',
        'id_laboran',
        'id_ruangan'
    ];
}
