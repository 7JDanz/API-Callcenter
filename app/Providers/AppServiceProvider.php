<?php

namespace App\Providers;

use App\Models\App\Conexion;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
        $configuracionesDB = Conexion::select()
            ->get()
            ->transform(function ($conexion) {
                $dbHost = empty($conexion->instancia)?$conexion->servidor:$conexion->servidor."\\".$conexion->instancia;
                return [
                    "nombre"=>$conexion->nombre,
                    "driver"=>"sqlsrv",
                    'url' => "",
                    'host' => $dbHost,
                    'port' => $conexion->puerto,
                    'database' => $conexion->bdd,
                    'username' => $conexion->usuario,
                    'password' => $conexion->clave,
                    'charset' => 'utf8',
                    'prefix' => '',
                    'prefix_indexes' => true,
                ];
            })
            ->keyBy("nombre")
            ->toArray() ;
        $conexiones = config('database.connections')+$configuracionesDB;
        config(['database.connections' =>$conexiones]);
        */
    }
}
