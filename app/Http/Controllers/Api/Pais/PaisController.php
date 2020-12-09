<?php

namespace App\Http\Controllers\Api\Pais;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/pais",
     *      operationId="getPaisList",
     *      tags={"Pais"},
     *      summary="Lista todos los Paises",
     *      description="Retorna todos los paises",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
    *         @OA\JsonContent(type="object",
    *              @OA\Property(property="Pais", type="array",description="Listado de países",
    *                  @OA\Items(type="object",
    *                      @OA\Property(property="IDPais", type="integer",description="ID de País"),
    *                      @OA\Property(property="Pais", type="string",description="Nombre de País"),
    *                      @OA\Property(property="BaseFactura", type="integer",description="Monto para consumidor final"),
    *                      @OA\Property(property="Cadenas", type="array",description="Cadenas pertenecientes al País",
    *                           @OA\Items(type="object",
    *                               @OA\Property(property="IDCadena", type="integer",description="ID de la Cadena"),
    *                               @OA\Property(property="Cadena", type="string",description="Nombre de la Cadena"),
    *                               @OA\Property(property="ServicioDomicilio", type="integer",description="Valor Servicio a Domicilio"),
    *                           ),
    *                      ),
    *                      @OA\Property(property="Impuestos", type="array",description="Impuestos aplicados al País",
    *                           @OA\Items(type="object",
    *                               @OA\Property(property="IDImpuesto", type="string",description="ID del Impuesto"),
    *                               @OA\Property(property="Descripcion", type="string",description="Descripción del Impuesto"),
    *                               @OA\Property(property="Porcentaje", type="integer",description="Porcentaje del Impuesto"),
    *                           ),
    *                      ),
    *                      @OA\Property(property="Ciudades", type="array",description="Ciudades del País",
    *                           @OA\Items(type="object",
    *                               @OA\Property(property="IDCiudad", type="integer",description="ID de la Ciudad"),
    *                               @OA\Property(property="Ciudad", type="string",description="Nombre de la Ciudad"),
    *                           ),
    *                      ),
    *                  ),
    *              ),
    *           ),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index(){
        $pais = Pais::all();
        return  $pais;
    }
}
