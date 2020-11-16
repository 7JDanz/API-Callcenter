<?php

namespace App\Http\Controllers\Api\Usuarios;

use App\Http\Controllers\Controller;
use App\Models\MdmClientes\Cliente;
use Illuminate\Http\Request;
use App\Services\UsersPosService;

class UsuariosPosController extends Controller
{
    public function validarDatosAcceso(){
        $usersPosService = new UsersPosService();
        return json_encode($usersPosService->validarLogin("xtwo","222222222"));
    }

}
