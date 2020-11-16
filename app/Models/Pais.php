<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;
    protected $table = 'callcenter.pais';
    /*
    protected $fillable = [
        'Data'
    ];
    */
    protected $casts = [
        'Pais' => 'json',
    ];
}
