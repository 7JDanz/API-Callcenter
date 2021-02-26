<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;
class Locales extends MongoModel
{
       use HasFactory;
       protected $connection = "mongodb";
       protected $table = 'RestaurantePoligonos';
       protected $fillable = ['IDRestaurante','codZipCode','invertir','poligonoCobertura'];

       public function restaurante()
       {
           return $this->belongsTo(Restaurante::class, "IDRestaurante", "IDRestaurante");
       }

}
