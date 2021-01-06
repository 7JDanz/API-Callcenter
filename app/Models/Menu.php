<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'trade.menu';
    //protected $primaryKey = 'IDMenu';
    protected $connection='sqlsrv_mxp_ecu';

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
