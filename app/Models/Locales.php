<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;
class Locales extends MongoModel
{
       use HasFactory;
       protected $connection = "mongodb";
       protected $table = 'Locales';

       public function restaurante()
       {
           return $this->belongsTo(Restaurante::class, "rst_id", "IDRestaurante");
       }
}
