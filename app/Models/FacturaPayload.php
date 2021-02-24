<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class FacturaPayload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

    protected $fillable = [
        'detalle',
        'modificadores',
        'cabecera',
        'formasPago',
        'IDFactura',
        'IDCadena',
        'IDRestaurante',
        'status',
        'datos_inyeccion',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'datos_inyeccion'
    ];

    public function getDateFormat()
    {
        return env("FORMATO_FECHAS","Y-d-m H:i:s.v");
    }
}
