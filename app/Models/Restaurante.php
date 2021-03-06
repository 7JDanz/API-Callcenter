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
    protected $hidden = [
        'BrutoServicioDomicilio',
        'IvaServicioDomicilio',
        'NetoServicioDomicilio',
        'IDRecargoDomicilio',
        'IDCategoria',
        'UrlRestaurante',
        'UrlRestauranteBackup',
        'Impuesto1',
        'Impuesto2',
        'Impuesto3',
        'Impuesto4',
        'Impuesto5',
        'PedidoMinimo',
    ];
    protected $casts = [
        'MenusRestaurante' => 'json',
        'ServiciosRestaurante' => 'json',
    ];

    public function localesMongo()
    {
		return $this->hasOne(Locales::class, 'IDRestaurante' ,'IDRestaurante');
    }

    public function horariosAtencion()
    {
		return $this->hasMany(HorarioAtencionRestaurante::class, 'IDRestaurante' ,'IDRestaurante');
    }


}
