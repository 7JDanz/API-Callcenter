<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;
    protected $table = 'callcenter.subcategoria'; 
    protected $connection='sqlsrv_mxp_ecu';
    
    protected $fillable = [
        'IDSubcategoria',
        'Subcategoria',
        'IDMenu',
        'default',
        
    ];   

}
