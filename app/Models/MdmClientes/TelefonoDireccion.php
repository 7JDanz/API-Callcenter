<?php

namespace App\Models\MdmClientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelefonoDireccion extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;
    protected $connection = 'sqlsrv_clientes';
    protected $table = 'mdm_ecu.TelefonoDireccion';
    protected $hidden = ['FechaLimpieza','CodigoCliente','CodigoTelefono','Ruc_Cedula','CodigoDireccion','Sistema'];

    public function cliente()
    {
        return $this->belongsTo(ClienteTelefono::class);
    }
}
