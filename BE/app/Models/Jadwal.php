<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    /** @use HasFactory<\Database\Factories\JadwalFactory> */
    use HasFactory, Filterable;

    protected $fillable = [
        'periode_tahun_id',
        'hari_jadwal',
        'jenis_jadwal',
        'waktu_mulai',
        'waktu_selesai',
        'makul_id',
        'status',
        'prodi_id',
        'kelas',
        'ruangan_id',
        'dosen_1',
        'dosen_2',
        'laboran_id'
    ];
    public function dosen(): BelongsTo{
        return $this->belongsTo(Dosen::class);
    }
    public function laboran(): BelongsTo{
        return $this->belongsTo(Laboran::class);
    }
    public function makul(): BelongsTo{
        return $this->belongsTo(Makul::class);
    }
    public function prodi(): BelongsTo{
        return $this->belongsTo(Prodi::class);
    }
    public function ruangan(): BelongsTo{
        return $this->belongsTo(Ruangan::class);
    }
}
