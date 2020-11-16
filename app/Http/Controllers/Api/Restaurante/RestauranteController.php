<?php

namespace App\Http\Controllers\Api\Restaurante;

use App\Http\Controllers\Controller;
use App\Models\Geolocalizacion;
use App\Models\Restaurante;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="API Call Center", version="4.0")
 *
 * @OA\Server(url="/clienteskfc/public/api/")
 */

class RestauranteController extends Controller
{


    public function poligonoCobertura(Request $request)
    {

        $poligono = Geolocalizacion::where("id_restaurante",  $request->rstId)->firstOrFail();
        return $poligono;
    }


    /**
     * @OA\Get(
     *      path="/restaurante/IDRestaurante/{IDRestaurante}",
     *      operationId="getRestauranteId",
     *      tags={"Restaurante"},
     *      summary="Lista restaurantes por IDRestaurante",
     *      description="Retorna restaurante por IDRestaurante",
     *
     *      @OA\Parameter(
     *          description="Puede buscar por uno ó varios Ids separados por coma /4 ó /4,20",
     *          name="IDRestaurante",
     *          in="path",
     *          required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function restaurantePorId($id)
    {
        $myArray = explode(',', $id);
        $restaurante = Restaurante::whereIn("IDRestaurante", $myArray)->get();
        return response()->json([
            'Restaurante' => $restaurante
        ]);
    }

    /**
     * @OA\Get(
     *      path="/restaurante/IDCadena/{IDCadena}",
     *      operationId="getRestaurantePorIDCadena",
     *      tags={"Restaurante"},
     *      summary="Lista restaurantes por IDCadena",
     *      description="Retorna restaurante por IDCadena",
     *
     *      @OA\Parameter(
     *          description="Puede buscar por uno ó varios Ids separados por coma /10 ó /12,10",
     *          name="IDCadena",
     *          in="path",
     *          required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
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
    public function restaurantePorCadena($cadena)
    {
        $restaurante = Restaurante::where("IDCadena", $cadena)->get();
        return response()->json([
            'Restaurante' => $restaurante
        ]);
    }
}
