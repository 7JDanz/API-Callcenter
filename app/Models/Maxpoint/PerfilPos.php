<?php

namespace App\Models\Maxpoint;

use App\Models\AppModel;

class PerfilPos extends AppModel
{
    protected $connection = 'sqlsrv_maxpoint';
    protected $table = 'Perfil_Pos';
    protected $primaryKey = 'IDPerfilPos';
    protected $keyType = 'string';
    public $incrementing = false;
}
