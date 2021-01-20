<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class MenuAgrupacion extends Model
{
    use HasFactory;
    protected $table = 'callcenter.menu_productos_categoria';

    public function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }

    protected $fillable = [
        'IDMenu',
        'IDCategoria',
        'categoria',
        'productos',

    ];

    protected $hidden = [
        'IDMenu',
    ];

    protected $casts = [
        'productos' => 'json',
    ];

    public function menu()
    {
        return $this->belongsTo('App\Models\Menu');
    }
}
