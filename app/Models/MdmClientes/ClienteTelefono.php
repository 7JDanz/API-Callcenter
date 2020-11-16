<?php

namespace App\Models\MdmClientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteTelefono extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $connection = 'sqlsrv_clientes';
    protected $table = 'mdm_ecu.ClienteTelefono';
    protected $hidden = ['FechaLimpieza','CodigoCliente','CodigoTelefono','Ruc_Cedula','Nombre','Sistema'];

    public function cliente()
    {

        return $this->belongsTo(Cliente::class);
    }

    public function direcciones()
    {
        return $this->hasMany(TelefonoDireccion::class,
        ["CodigoCliente","CodigoTelefono"],
        ["CodigoCliente","CodigoTelefono"]
    );

    }

}
