<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    /** @use HasFactory<\Database\Factories\RuanganFactory> */
    use HasFactory;
        protected $fillable = [
        'kode_ruangan',
        'nama_ruangan'
    ];
    
    public function jadwal():HasMany{
        return $this->hasMany(Jadwal::class);
    }
}
