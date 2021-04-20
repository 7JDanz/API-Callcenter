<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Cadena extends Model
{
    use HasFactory;
    protected $table = 'trade.cadena';
    //protected $primaryKey = 'IDMenu';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }
    //IDCadena	CodCadena	Descripcion	IDCadena	estado

    protected $fillable = [
        'IDCadena',
        'Cadena',
        'ServicioDomicilio',
        'IDPais',
        'Estado',
    ];

    protected $hidden = [
        'Impuesto1',
        'Impuesto2',
        'Impuesto3',
        'Impuesto4',
        'Impuesto5',
        'Ambiente',
        'AmbienteCodigo',
    ];

    protected $casts = [
        'IDCadena' => 'integer',
        'ServicioDomicilio' => 'float',
        'IDPais' => 'integer',
    ];

}
