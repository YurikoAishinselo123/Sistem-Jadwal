<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaktuPerkuliahan extends Model
{
    /** @use HasFactory<\Database\Factories\WaktuPerkuliahanFactory> */
    use HasFactory;

    protected $fillable = [
        'jam',
    ];
}
