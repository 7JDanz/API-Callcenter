<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Restaurante extends Model
{
    use HasFactory;
    protected $table = 'trade.restaurante';
    protected $primaryKey = 'IDRestaurante';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

    protected $fillable = [
        'IDRestaurante',
        'IDTienda',
        'Nombre',
        'Direccion',
        'Telefono',
        'Ciudad',
        'Latitud',
        'Longitud',
        'IDCadena',
        'MenusRestaurante',
        'ServiciosRestaurante',
        'NombreComercial',
        'IDCategoria',
        'Servicios',
        'CentroComercial',
        'EstaAtendiendo',
        'CookTime',
        'TieneDomicilio',
        'TieneKiosko',
        'TienePickup',
    ];

    protected $casts = [
        'MenusRestaurante' => 'json',
        'ServiciosRestaurante' => 'json',
    ];

    public function geolocalizacion()
    {
		return $this->hasOne('App\Models\Geolocalizacion', 'id_restaurante' ,'IDRestaurante');
    }

    public function horariosAtencion()
    {
		return $this->hasMany(HorarioAtencionRestaurante::class, 'IDRestaurante' ,'IDRestaurante');
    }


}
