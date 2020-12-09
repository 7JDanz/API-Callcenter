<?php

namespace App\Models\Maxpoint;

use App\Models\AppModel;
use Config;

class Cadena extends AppModel
{
    protected $table = 'Cadena';
    protected $primaryKey = 'cdn_id';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }
}
