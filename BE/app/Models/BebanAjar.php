<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BebanAjar extends Model
{
    /** @use HasFactory<\Database\Factories\BebanAjarFactory> */
    use HasFactory, Filterable;

    protected $fillable = [
        'id_dosen',
        'beban_sesi',
        'beban_sks'
    ];
}
