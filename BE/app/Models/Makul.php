<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Makul extends Model
{
    /** @use HasFactory<\Database\Factories\MakulFactory> */
    use HasFactory;
        protected $fillable = [
        'kode_makul',
        'nama_makul',
        'sks_teori_makul',
        'sks_praktik_makul'
    ];
    
    public function jadwal():HasMany{
        return $this->hasMany(Jadwal::class);
    }
}
