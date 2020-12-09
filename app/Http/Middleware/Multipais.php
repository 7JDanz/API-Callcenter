<?php

namespace App\Http\Middleware;

use App\Util\Helpers;
use Closure;
use Illuminate\Support\Facades\Config;

class Multipais {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $pais=$request->route('pais');
        $nombreConexion = Helpers::buscarNombreConexionAzurePorPais($pais);
        Config::set("NOMBRE_CONEXION_AZURE",$nombreConexion);

        $nombreConexionClientes = Helpers::buscarNombreConexionClientesPorPais($pais);
        Config::set("NOMBRE_CONEXION_CLIENTES", $nombreConexionClientes);

        Config::set("PAIS_RUTA_PETICION",$pais);
        return $next($request);
    }

}
