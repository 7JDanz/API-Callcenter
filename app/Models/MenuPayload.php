<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPayload extends Model
{
    use HasFactory;
    protected $table = 'dbo.Menu_Payload';
    protected $primaryKey = 'id';
    protected $connection='sqlsrv_mxp_ecu';
    
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
