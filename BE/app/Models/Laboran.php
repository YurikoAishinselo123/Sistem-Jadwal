<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboran extends Model
{
    /** @use HasFactory<\Database\Factories\LaboranFactory> */
    use HasFactory;
        protected $fillable = [
        'kode_laboran',
        'nama_laboran',
    ];
    
    public function jadwal():HasMany{
        return $this->hasMany(Jadwal::class);
    }
}
