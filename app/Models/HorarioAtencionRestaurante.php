<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioAtencionRestaurante extends Model
{
    use HasFactory;
    protected $table = 'trade.horario_atencion_restaurante';
    protected $primaryKey = 'IDHorarioAtencionRestaurante';
    protected $connection = 'sqlsrv_mxp_ecu';

}
