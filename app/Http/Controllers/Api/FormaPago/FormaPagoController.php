<?php

namespace App\Http\Controllers\Api\FormaPago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormaPago;

class FormaPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pais,$cadena)
    {
        //
        $formaPago = FormaPago::where("IDCadena", $cadena)->get();
        return response()->json([
            'FormasPago' => $formaPago
        ]);
    }

}
