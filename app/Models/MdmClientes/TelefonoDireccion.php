<?php

namespace App\Models\MdmClientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class TelefonoDireccion extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;
    protected $connection = 'sqlsrv_clientes';
    protected $table = 'mdm_ecu.TelefonoDireccion';
    protected $hidden = ['FechaLimpieza','CodigoCliente','CodigoTelefono','Ruc_Cedula','CodigoDireccion','Sistema','FechaCarga'];

    public function cliente()
    {
        return $this->belongsTo(ClienteTelefono::class);
    }

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_CLIENTES");
    }
}
