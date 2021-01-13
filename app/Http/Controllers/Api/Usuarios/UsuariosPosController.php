<?php

namespace App\Http\Controllers\Api\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\MdmClientes\Cliente;
use Illuminate\Http\Request;
use App\Services\UsersPosService;

class UsuariosPosController extends Controller
{
    //TODO: traer el pais_id a partir del middleware
    public function validarDatosAcceso(Request $request) {
        $result = $request->json()->all();
        $usersPosService = new UsersPosService();
        return json_encode($usersPosService->validarLogin($result['usuario'],$result['clave'],$result['pais_id']));
    }

    public function actualizar_usuarios(Request $request) {
        $usersPosService = new UsersPosService();
        return $usersPosService->update_users_batch();
    }
}
