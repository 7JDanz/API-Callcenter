<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class FacturaPayloadCabecera extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'codigoApp',
        'medio',
        'dispositivo',
        'codRestaurante',
        'identificacionCliente',
        'consumidorFinal',
        'es_nueva_direccion',
        'nombresCliente',
        'direccionCliente',
        'emailCliente',
        'fechaPedido',
        'telefonoCliente',
        'calle1Domicilio',
        'calle2Domicilio',
        'numDirecciondomicilio',
        'observacionesDomicilio',
        'codZipCode',
        'tipoInmueble',
        'totalFactura',
        'observacionesPedido',
        'operador',
        'perfilOperador',
        'latitud',
        'longitud',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function __construct()
    {
        $this->codigoApp = 'codigoApp';
        $this->medio = 'medio';
        $this->dispositivo = 'dispositivo';
        $this->codRestaurante = 0;
        $this->identificacionCliente = 'identificacionCliente';
        $this->consumidorFinal = false;
        $this->es_nueva_direccion = true;
        $this->nombresCliente = 'nombresCliente';
        $this->direccionCliente = 'direccionCliente';
        $this->emailCliente = 'emailCliente';
        $this->fechaPedido = 'fechaPedido';
        $this->telefonoCliente = 'telefonoCliente';
        $this->calle1Domicilio = 'calle1Domicilio';
        $this->calle2Domicilio = 'calle2Domicilio';
        $this->numDirecciondomicilio = 'numDirecciondomicilio';
        $this->observacionesDomicilio = 'observacionesDomicilio';
        $this->codZipCode = 'codZipCode';
        $this->tipoInmueble = 0;
        $this->totalFactura = 0;
        $this->observacionesPedido = 'observacionesPedido';
        $this->operador = 'operador';
        $this->perfilOperador = 'perfilOperador';
        $this->latitud = 'latitud';
        $this->longitud = 'longitud';
    }

    public function getDateFormat()
    {
        return env("FORMATO_FECHAS","Y-d-m H:i:s.v");
    }
}
