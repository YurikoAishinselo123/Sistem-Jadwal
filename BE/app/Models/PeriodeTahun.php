<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeTahun extends Model
{
    /** @use HasFactory<\Database\Factories\PeriodeTahunFactory> */
    use HasFactory;
    protected $fillable = [
        'periode',
        'status',
        'tanggal_mulai',
        'tanggal_selesai'
    ];
    public function jadwal():HasMany{
        return $this->hasMany(Jadwal::class);
    }
}
