<?php

namespace App\Models\MdmClientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class Cliente extends Model
{
    use HasFactory;

    //protected $connection = 'sqlsrv_clientes';
    protected $table = 'mdm_ecu.Cliente';
    protected $primaryKey = 'CodigoCliente';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $hidden = ['FechaLimpieza','CodigoCliente','CorreoValido','Sexo','EstadoCivil','Profesion','NivelEstudio','DesCiudadano','TipoCliente','Telefono','Celular','CategorizarSueldo'];


    public function telefonos()
    {
        return $this->hasMany(ClienteTelefono::class,"CodigoCliente","CodigoCliente");
    }

    public function direcciones()
    {
        return $this->hasMany(TelefonoDireccion::class,"CodigoCliente","CodigoCliente");
    }

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_CLIENTES");
    }
}
