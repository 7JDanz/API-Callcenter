<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class CadenaColeccionDeDatos extends Model
{
    use HasFactory;
    protected $table = 'CadenaColeccionDeDatos';
    protected $primaryKey = "ID_CadenaColeccionDeDatos";
 
    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

}
