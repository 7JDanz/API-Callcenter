<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
class ColeccionDeDatosCadena extends Model
{
    use HasFactory;
    protected $table = 'ColeccionDeDatosCadena';
    protected $primaryKey = "ID_ColeccionDeDatosCadena";

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

   
}
