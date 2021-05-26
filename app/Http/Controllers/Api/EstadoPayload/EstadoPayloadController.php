<?php

namespace App\Http\Controllers\Api\EstadoPayload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstadoPayload;
use App\Models\FacturaPayload;
Use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;
use App\Classes\Utilities;

class EstadoPayloadController extends Controller
{
    public function get(Request $request) {
        $id = $request['IDFactura'];
        if ($id == null) {
           $estado_payloads = EstadoPayload::get();
           return response()->json($estado_payloads,200);
        } else {
           $estado_payload = EstadoPayload::where('IDFactura', $id)->first();
           if ($estado_payload) {
               return response()->json($estado_payload,200);
           } else {
               return response()->json(["Mensaje"=>"factura no encontrada"],400);
           }
        }
    }


    public function post(Request $request, $pais) {
        $data = $request->json()->all();
        $IDFactura = $data['IDFactura'];
        $cfac_id = $data['cfac_id'];
        $estado = $data['estado'];
        $toReturn = false;
        $prev_estado_payload = EstadoPayload::where('IDFactura', $request['IDFactura'])->first();
        if ($prev_estado_payload) {
            $prev_estado_payload->update([
                'estado'=>$estado,
            ]);
            $toReturn = true;
        } else {
            $new_estado_payload = new EstadoPayload();
            $new_estado_payload->IDFactura = $IDFactura;
            $new_estado_payload->cfac_id = $cfac_id;
            $new_estado_payload->estado = $estado;
            $new_estado_payload->save();
            $toReturn = true;
        }
        DB::beginTransaction();
        $factura_payload = FacturaPayload::where('IDFactura', $request['IDFactura'])->first();
        $now = date("Y-m-d H:i:s");
        $factura_payload->update([
            'status'=>array_push($factura_payload->status, ["timestamp"=>$now, "status"=>$estado]),
        ]);
        DB::commit();
        return response()->json($toReturn,200);
    }
}
