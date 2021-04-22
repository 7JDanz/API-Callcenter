<?php

namespace App\Http\Controllers\Api\Cadena;

use App\Http\Controllers\Controller;
use App\Models\Cadena;

class CadenaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pais)
    {
        //
        $Cadena = Cadena::all();
        return response()->json([
            'Cadenas' => $Cadena
        ]);
    }

}
