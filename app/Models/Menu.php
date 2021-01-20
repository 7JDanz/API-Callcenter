<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'trade.menu';
    //protected $primaryKey = 'IDMenu';

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
        'menu',
        'estado',
        'IDCadena',
    ];

    protected $hidden = [
        'IDCanal',
        'IDMedio',
    ];

    public function menu_agrupacion()
    {
        return $this->hasMany('App\Models\MenuAgrupacion');
    }

    public function menu_categorias(){
        return $this->hasMany('App\Models\MenuCategorias');
    }
}
