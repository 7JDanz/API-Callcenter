<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class HorarioAtencionRestaurante extends Model
{
    use HasFactory;
    protected $table = 'trade.horario_atencion_restaurante';
    protected $primaryKey = 'IDHorarioAtencionRestaurante';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

    public function __construct()
    {
        $this->connection = Config::get("NOMBRE_CONEXION_AZURE");
    }

}
