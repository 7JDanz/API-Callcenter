<?php

namespace App\Http\Controllers\Api\FacturaPayload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FacturaPayload;
Use Exception;
use Illuminate\Support\Facades\DB;

class FacturaPayloadController extends Controller
{
    public function get(Request $request, $pais) {
        $id = $request['IDCabeceraFactura'];
        if ($id == null) {
           return response()->json(FacturaPayload::get(),200);
        } else {
           $factura_payload = FacturaPayload::findOrFail($id);
           return response()->json($factura_payload,200);
        }
    }

    public function post(Request $request, $pais) {
        $data = $request->json()->all();
        $preview_factura_payload = FacturaPayload::where('IDCabeceraFactura', $data['IDCabeceraFactura'])->first();
        if ($preview_factura_payload) {
            return response()->json($preview_factura_payload,200);
        }
        $new_factura_payload = new FacturaPayload();
        $new_factura_payload->orden = $data['orden'];
        $new_factura_payload->IDCabeceraFactura = $data['IDCabeceraFactura'];
        $new_factura_payload->save();
        return response()->json($new_factura_payload,200);
    }

    public function put(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDCabeceraFactura', $data['IDCabeceraFactura'])->update([
               'orden'=>$data['orden'],
            ]);
            DB::commit();
            return response()->json($factura_payload,200);
         } catch (Exception $e) {
            return response()->json($e,400);
         }
    }

    public function delete(Request $request, $pais) {
        $id = $request['id'];
        return FacturaPayload::destroy($id);
    }

    public function inserta_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDCabeceraFactura', $data['IDCabeceraFactura'])->first();
        $orden = json_decode($factura_payload->orden);
        $new_producto = $data['producto'];
        $cantidad = $data['cantidad'];
        array_push($orden,["item"=>$new_producto, "cantidad"=>$cantidad]);
        try{
            DB::beginTransaction();
            $factura_payload->update([
                'orden'=>json_encode($orden),
            ]);
            DB::commit();
            return response()->json($factura_payload,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }
}
