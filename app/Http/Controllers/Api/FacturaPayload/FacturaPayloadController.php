<?php

namespace App\Http\Controllers\Api\FacturaPayload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FacturaPayload;

class FacturaPayloadController extends Controller
{
    public function get(Request $request, $pais) {

        return $pais . ' get ';
    }

    public function post(Request $request, $pais) {
        $data = $request->json()->all();
        $new_factura_payload = new FacturaPayload();
        $new_factura_payload->orden = $data['orden'];
        $new_factura_payload->IDCabeceraFactura = $data['IDCabeceraFactura'];
        $new_factura_payload->save();
        return $new_factura_payload;
    }

    public function put(Request $request, $pais) {
        return $pais . ' put ';
    }

    public function delete(Request $request, $pais) {
        return $pais . ' dalete ';
    }
}
