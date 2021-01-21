<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Pais extends Model
{
    use HasFactory;
    protected $table = 'paises';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

    /*
    protected $fillable = [
        'Data'
    ];
    */
    protected $casts = [
        'Pais' => 'json',
    ];
}
