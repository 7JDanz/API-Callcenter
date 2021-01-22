<?php

namespace App\Http\Controllers\Api\FacturaPayload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FacturaPayload;

class FacturaPayloadController extends Controller
{
    public function get(Request $request, $pais) {
        $id = $request['id'];
        if ($id == null) {
           return response()->json(FacturaPayload::get(),200);
        } else {
           $factura_payload = FacturaPayload::findOrFail($id);
           return response()->json($factura_payload,200);
        }
    }

    public function post(Request $request, $pais) {
        $data = $request->json()->all();
        $new_factura_payload = new FacturaPayload();
        $new_factura_payload->orden = $data['orden'];
        $new_factura_payload->IDCabeceraFactura = $data['IDCabeceraFactura'];
        $new_factura_payload->save();
        return response()->json($new_factura_payload,200);
    }

    public function put(Request $request, $pais) {
        return $pais . ' put ';
    }

    public function delete(Request $request, $pais) {
        return $pais . ' dalete ';
    }
}
