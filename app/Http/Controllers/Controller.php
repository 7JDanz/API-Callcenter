<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
    * @OA\Post(
    *     path="/oauth/token",
    *     tags={"Seguridad"},
    *     summary="Obtener Token",
    *     @OA\Parameter(
    *     in="header",
    *     name="grant_type",
    *     example="password"
    *     ),
    *     @OA\Parameter(
    *     in="header",
    *     name="client_id",
    *     example="3"
    *     ),
    *     @OA\Parameter(
    *     in="header",
    *     name="client_secret",
    *     example="cDxhCy3XOYpMjYJkYAkOQmExQF0doezve4AZtV69"
    *     ),
    *     @OA\Parameter(
    *     in="header",
    *     name="username",
    *     example="fsierra"
    *     ),
    *     @OA\Parameter(
    *     in="header",
    *     name="password",
    *     example="SVucd2fYX5KVaiL"
    *     ),
    *     @OA\Parameter(
    *     in="header",
    *     name="scope",
    *     example="*"
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Token de seguridad",
    *         @OA\JsonContent(type="object",
    *              @OA\Property(property="token_type", type="string",description="Tipo de Token"),
    *              @OA\Property(property="expires_in", type="integer",description="Tiempo en milisegundos"),
    *              @OA\Property(property="access_token", type="string",description="token de seguridad"),
    *              @OA\Property(property="refresh_token", type="string",description="token"),
    *          ),
    *     ),
    *     @OA\Response(response=404, description="Not Found"),
    *     )
    * )
    * @return JsonResponse
    */
}
