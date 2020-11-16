<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Geolocalizacion extends AppModel
{
    use HasFactory;

    protected $table ='geolocalizacion';
    protected $connection = 'sqlsrv';

    protected $fillable = ['name','id_restaurante','address','reference','status','type','keywords','map','properties','coordinates'];

    protected $casts = [
        'map' => 'array',
        'properties' => 'array',
        'coordinates' => 'array',
    ];


    public function restaurante()
    {
		return $this->belongsTo('App\Restaurante', 'IDRestaurante');
    }

}
