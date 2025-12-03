<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    /** @use HasFactory<\Database\Factories\DosenFactory> */
    use HasFactory;
        protected $fillable = [
        'kode_dosen',
        'nama_dosen',
    ];
    public function jadwal1():HasMany{
        return $this->hasMany(Jadwal::class, "dosen_1");
    }
    public function jadwal2():HasMany{
        return $this->hasMany(Jadwal::class, "dosen_2");
    }
}
