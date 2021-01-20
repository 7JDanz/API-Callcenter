<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;


class MenuPayload extends Model
{
    use HasFactory;
    protected $table = 'dbo.Menu_Payload';
    protected $primaryKey = 'id';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

    public function __construct()
    {
        $this->connection = Config::get("NOMBRE_CONEXION_AZURE");
    }

    protected $fillable = [
        'IDMenu',
        'IDCadena',
        'MenuAgrupacion',
        'status',

    ];

    protected $casts = [
        'MenuAgrupacion' => 'json',
        'MenuCategorias' => 'json',
    ];
}
