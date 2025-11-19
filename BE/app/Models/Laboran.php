<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboran extends Model
{
    /** @use HasFactory<\Database\Factories\LaboranFactory> */
    use HasFactory;
        protected $fillable = [
        'nama_laboran',
    ];
}
