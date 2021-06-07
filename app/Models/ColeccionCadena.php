<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ColeccionCadena extends Model
{
    use HasFactory;
    protected $table = 'ColeccionCadena';
    protected $primaryKey = "ID_ColeccionCadena";

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

    public function _cadena()
    {
        return $this->belongsTo(Cadena::class, "cdn_id", "IDCadena");
    }

}
