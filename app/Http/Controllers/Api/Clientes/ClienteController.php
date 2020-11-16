<?php

namespace App\Http\Controllers\Api\Clientes;

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

class ClienteController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/cliente/{cedula}",
    *     summary="Mostrar cliente por documento de identidad",
    *     tags={"Cliente"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *     in="path",
    *     name="cedula"
    *
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar el cliente",
    *         @OA\JsonContent(type="object",
    *              @OA\Property(property="Ruc_Cedula", type="string",description="Documento de identidad del cliente"),
    *              @OA\Property(property="Nombre", type="string",description="Nombres del cliente"),
    *              @OA\Property(property="Correo", type="string",description="Email del cliente"),
    *              @OA\Property(property="telefonos", type="array",description="Teléfonos asociados al cliente",
    *                  @OA\Items(type="object",
    *                      @OA\Property(property="Telefono", type="string",description="Número de teléfono"),
    *                      @OA\Property(property="direcciones", type="array",description="Direcciones aosciadas al número de teléfono",
    *                           @OA\Items(type="object",
    *                               @OA\Property(property="Telefono", type="string",description="Número de teléfono"),
    *                               @OA\Property(property="Direccion", type="string",description="Dirección"),
    *                           ),
    *                      ),
    *                  ),
    *              ),
    *          ),
    *     ),
    *     @OA\Response(response=401, description="Unauthorized"),
    *     @OA\Response(response=404, description="Not Found"),
    *     )
    * )
    */
    public function cliente($documento){
        return Cliente::where("Ruc_Cedula",$documento)
            ->with(["telefonos","telefonos.direcciones"])
            ->get();
    }

    /**
    * @OA\Get(
    *     path="/api/cliente-email/{email}",
    *     tags={"Cliente"},
    *     summary="Mostrar cliente por email",
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *     in="path",
    *     name="email",
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar el cliente",
    *         @OA\JsonContent(type="object",
    *              @OA\Property(property="Ruc_Cedula", type="string",description="Documento de identidad del cliente"),
    *              @OA\Property(property="Nombre", type="string",description="Nombres del cliente"),
    *              @OA\Property(property="Correo", type="string",description="Email del cliente"),
    *              @OA\Property(property="telefonos", type="array",description="Teléfonos asociados al cliente",
    *                  @OA\Items(type="object",
    *                      @OA\Property(property="Telefono", type="string",description="Número de teléfono"),
    *                      @OA\Property(property="direcciones", type="array",description="Direcciones aosciadas al número de teléfono",
    *                           @OA\Items(type="object",
    *                               @OA\Property(property="Telefono", type="string",description="Número de teléfono"),
    *                               @OA\Property(property="Direccion", type="string",description="Dirección"),
    *                           ),
    *                      ),
    *                  ),
    *              ),
    *          ),
    *     ),
    *     @OA\Response(response=401, description="Unauthorized"),
    *     @OA\Response(response=404, description="Not Found"),
    *     )
    * )
    */
    public function clientePorEmail($email){
        return Cliente::where("Correo",$email)
            ->with(["telefonos","telefonos.direcciones"])
            ->get();
    }

    /**
    * @OA\Get(
    *     path="/api/cliente-telefono/{telefono}",
    *     tags={"Cliente"},
    *     summary="Mostrar clientes por número de teléfono",
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *     in="path",
    *     name="telefono",
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar el cliente",
    *         @OA\JsonContent(type="object",
    *              @OA\Property(property="Ruc_Cedula", type="string",description="Documento de identidad del cliente"),
    *              @OA\Property(property="Nombre", type="string",description="Nombres del cliente"),
    *              @OA\Property(property="Correo", type="string",description="Email del cliente"),
    *              @OA\Property(property="telefonos", type="array",description="Teléfonos asociados al cliente",
    *                  @OA\Items(type="object",
    *                      @OA\Property(property="Telefono", type="string",description="Número de teléfono"),
    *                      @OA\Property(property="direcciones", type="array",description="Direcciones aosciadas al número de teléfono",
    *                           @OA\Items(type="object",
    *                               @OA\Property(property="Telefono", type="string",description="Número de teléfono"),
    *                               @OA\Property(property="Direccion", type="string",description="Dirección"),
    *                           ),
    *                      ),
    *                  ),
    *              ),
    *          ),
    *     ),
    *     @OA\Response(response=401, description="Unauthorized"),
    *     @OA\Response(response=404, description="Not Found"),
    *     )
    * )
    * @return JsonResponse
    */
    public function clientePorTelefono($telefono){
        return Cliente::whereHas('telefonos', function($q) use($telefono)
        {
            $q->where('Telefono',$telefono);

        })
            ->with(["telefonos","telefonos.direcciones"])
            ->get();
    }

}
