<?php

namespace App\Http\Controllers\Api\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\MdmClientes\Cliente;
use Illuminate\Http\Request;
use App\Services\UsersPosService;

class UsuariosPosController extends Controller
{
    public function validarDatosAcceso(Request $request) {
        $result = $request->json()->all();
        $usersPosService = new UsersPosService();
        return json_encode($usersPosService->validarLogin($result['usuario'],$result['clave']));
    }

    public function actualizar_usuarios(Request $request) {
        $usersPosService = new UsersPosService();
        return $usersPosService->update_users_batch();
    }

    public function insert_user(Request $request) {
        $result = $request->json()->all();
        $usersPosService = new UsersPosService();
        return $usersPosService->insert_user($result['name'], $result['email'], $result['password'], $result['std_descripcion'], $result['prf_descripcion'], $result['pais_id'], $result['usuario'], $result['IDUsersPos']);
    }
}
