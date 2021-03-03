<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class FormaPago extends Model
{
    use HasFactory;
    protected $table = 'trade.forma_pago';
    //protected $primaryKey = 'IDMenu';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }
    //IDFormapago	CodFormaPago	Descripcion	IDCadena	estado

    protected $fillable = [
        'IDFormapago',
        'CodFormaPago',
        'Descripcion',
        'IDCadena',
    ];

    protected $hidden = [
        'estado',
    ];

}
