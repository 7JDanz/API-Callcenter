<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategorias extends Model
{
    use HasFactory;
    protected $table = 'callcenter.menu_productos_subcategoria';
    
    protected $fillable = [        
        'IDCategoria',
        'categoria',
        'productos',
        'IDMenu',
        'IDSubcategoria',
        
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
