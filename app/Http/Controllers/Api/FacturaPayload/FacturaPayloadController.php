<?php

namespace App\Http\Controllers\Api\FacturaPayload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FacturaPayload;
Use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class FacturaPayloadController extends Controller
{
    public function get(Request $request, $pais) {
        $id = $request['IDFactura'];
        if ($id == null) {
           return response()->json(FacturaPayload::get(),200);
        } else {
           $factura_payload = FacturaPayload::findOrFail($id);
           return response()->json($factura_payload,200);
        }
    }

    public function post(Request $request, $pais) {
        $data = $request->json()->all();
        $preview_factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->first();
        if ($preview_factura_payload) {
            return response()->json($preview_factura_payload,200);
        }
        $new_factura_payload = new FacturaPayload();
        $new_factura_payload->orden = $data['orden'];
        $new_factura_payload->cabecera = $data['cabecera'];
        $new_factura_payload->valores = $data['valores'];
        $new_factura_payload->IDFactura = uniqid();
        $new_factura_payload->save();
        return response()->json($new_factura_payload,200);
    }

    public function put(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->update([
               'orden'=>$data['orden'],
               'valores'=>$data['valores'],
               'cabecera'=>$data['cabecera'],
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
        $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->first();
        $orden = json_decode($factura_payload->orden);
        $new_producto = $data['producto'];
        $cantidad = $data['cantidad'];
        $item = new stdClass();
        $item->id = uniqid();
        $item->producto = $new_producto;
        $item->cantidad = $cantidad;
        array_push($orden, $item);
        try{
            DB::beginTransaction();
            $factura_payload->update([
                'orden'=>json_encode($orden),
            ]);
            DB::commit();
            return response()->json($orden,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function borra_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->first();
        $orden = json_decode($factura_payload->orden);
        $id_producto_borrar = $data['id'];
        $new_orden = [];
        $eliminado = false;
        foreach($orden as $item) {
            $detalle_item = (object) $item;
            if ($detalle_item->id == $id_producto_borrar) {
                $eliminado = true;
            } else {
                array_push($new_orden, $item);
            }
        }
        if ($eliminado) {
            try{
                DB::beginTransaction();
                $factura_payload->update([
                    'orden'=>json_encode($new_orden),
                ]);
                DB::commit();
                return response()->json($new_orden,200);
            } catch (Exception $e) {
                return response()->json($e,400);
            }
        } else {
            return response()->json("producto no encontrado", 400);
        }
    }
}
