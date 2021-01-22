<?php

namespace App\Http\Controllers\Api\OrdenPedido;

use App\Http\Controllers\Controller;
use App\Models\MdmClientes\Cliente;
use Illuminate\Http\Request;

/**
* @OA\Info(title="API Callcenter", version="1.0")
*
* @OA\SecurityScheme(
*   securityScheme="bearerAuth",
*   in="header",
*   name="bearerAuth",
*   type="http",
*   scheme="bearer",
*   bearerFormat="JWT",
* ),
*/

class OrdenPedidoController extends Controller
{
    function get(Request $request,$pais) {
        return 'get';
    }

    function post(Request $request,$pais) {
        return 'post';
    }

    function put(Request $request,$pais) {
        return 'put';
    }

    function delete(Request $request,$pais) {
        return 'delete';
    }
}
