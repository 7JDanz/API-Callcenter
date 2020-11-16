<?php

namespace App\Http\Controllers\Api\Pais;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    /**
     * @OA\Get(
     *      path="/pais",
     *      operationId="getPaisList",
     *      tags={"Pais"},
     *      summary="Lista todos los Paises",
     *      description="Retorna todos los paises",
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
    public function index(){
        $pais = Pais::all();
        return  $pais;
    }    
}
