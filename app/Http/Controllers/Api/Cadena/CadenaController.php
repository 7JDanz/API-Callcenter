<?php

namespace App\Http\Controllers\Api\Cadena;
use App\Http\Controllers\Controller;
use App\Models\Cadena;
use App\Models\ColeccionDeDatosCadena;

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
        $cadena = Cadena::all();
        return response()->json([
            'Cadenas' => $cadena
        ]);
    }

  
    public function obtenerEstados( $pais, $cadena )
    {
 
        return ColeccionDeDatosCadena::select('ColeccionDeDatosCadena.ID_ColeccionDeDatosCadena as IDCodigo' ,'ColeccionDeDatosCadena.Descripcion as descripcion'
        , 'CadenaColeccionDeDatos.variableV as orden'
        )
        ->join('ColeccionCadena', 'ColeccionCadena.ID_ColeccionCadena', 'ColeccionDeDatosCadena.ID_ColeccionCadena')
        ->join('CadenaColeccionDeDatos', 'CadenaColeccionDeDatos.ID_ColeccionDeDatosCadena', 'ColeccionDeDatosCadena.ID_ColeccionDeDatosCadena')
        ->where('ColeccionCadena.Descripcion','ESTADOS API CALL CENTER')
        ->where('ColeccionCadena.isActive', 1)
        ->where('ColeccionDeDatosCadena.isActive', 1)
        ->where('ColeccionCadena.cdn_id', $cadena)
        ->orderBy('CadenaColeccionDeDatos.variableV','ASC')
        ->get()
        ;
    }

}
