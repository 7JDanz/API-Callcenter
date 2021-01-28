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
           $factura_payloads = FacturaPayload::get();
           $toReturn = [];
           foreach($factura_payloads as $factura_payload) {
               $factura_payload->orden = json_decode($factura_payload->orden);
               $factura_payload->cabecera = json_decode($factura_payload->cabecera);
               $factura_payload->valores = json_decode($factura_payload->valores);
               $factura_payload->satus = $factura_payload->status;
               array_push($toReturn, $factura_payload);
           }
           return response()->json($toReturn,200);
        } else {
           $factura_payload = FacturaPayload::where('IDFactura', $id)->first();
           $factura_payload->orden = json_decode($factura_payload->orden);
           $factura_payload->cabecera = json_decode($factura_payload->cabecera);
           $factura_payload->valores = json_decode($factura_payload->valores);
           $factura_payload->satus = $factura_payload->status;
           if ($factura_payload) {
            return response()->json($factura_payload,200);
           } else {
            return response()->json("factura no encontrada",400);
           }
        }
    }

    public function post(Request $request, $pais) {
        $data = $request->json()->all();
        $new_id_factura = uniqid();
        $new_factura_payload = new FacturaPayload();
        $new_factura_payload->orden = json_encode($data['orden']);
        $new_factura_payload->cabecera = json_encode($data['cabecera']);
        $new_factura_payload->valores = json_encode($data['valores']);
        $new_factura_payload->status = 'activo';
        $new_factura_payload->IDFactura = $new_id_factura;
        $new_factura_payload->save();
        return response()->json($new_id_factura,200);
    }

    public function put(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->update([
               'orden'=>json_encode($data['orden']),
               'valores'=>json_encode($data['valores']),
               'cabecera'=>json_encode($data['cabecera']),
               'status'=>$data['status'],
            ]);
            DB::commit();
            return response()->json(true,200);
         } catch (Exception $e) {
            return response()->json($e,400);
         }
    }

    public function put_cabecera(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->update([
               'cabecera'=>json_encode($data['cabecera']),
            ]);
            DB::commit();
            return response()->json(true,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function put_valores(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->update([
               'valores'=>json_encode($data['valores']),
            ]);
            DB::commit();
            return response()->json(true,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function put_orden(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->update([
                'orden'=>json_encode($data['orden']),
            ]);
            DB::commit();
            return response()->json(true,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function delete(Request $request, $pais) {
        $IDFactura = $request['IDFactura'];
        try{
            DB::beginTransaction();
            $factura_payload = FacturaPayload::where('IDFactura', $IDFactura)->update([
                'estado'=>'inactivo',
            ]);
            DB::commit();
            return response()->json(true,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
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


    public function inserta_varios_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->first();
        $orden = json_decode($factura_payload->orden);
        $items = $data['items'];
        foreach($items as $item) {
            $cantidad = $item['cantidad'];
            $new_producto = $item['producto'];
            $new_item = new stdClass();
            $new_item->id = uniqid();
            $new_item->producto = $new_producto;
            $new_item->cantidad = $cantidad;
            array_push($orden, $new_item);
        }
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

    public function borra_varios_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->first();
        $orden = json_decode($factura_payload->orden);
        $ids_producto_borrar = $data['ids'];
        $new_orden = [];
        $eliminados = false;
        foreach($orden as $item) {
            $eliminado = false;
            $detalle_item = (object) $item;
            foreach($ids_producto_borrar as $id_producto_borrar) {
                if ($detalle_item->id == $id_producto_borrar) {
                    $eliminados = true;
                    $eliminado = true;
                }
            }
            if (!$eliminado) {
                array_push($new_orden, $item);
            }
        }
        if (!$eliminados) {
            return response()->json("no se eliminaron productos", 400);
        } else {
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
        }
    }

    public function calcula_valores(Request $request) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDFactura', $data['IDFactura'])->first();
        $costo_subtotal = 19; //aqui calcular el costo total como la suma de todo lo que agregue costo.
        $costos = new stdClass();
        $costos->SUBTOTAL = $costo_subtotal;
        $costos_insertar = $data['costos_insertar'];
        foreach($costos_insertar as $new_costo) {
          $etiqueta = $new_costo['etiqueta'];
          if ($new_costo["tipo"] == "calculo") {
            $new_valor = $costo_subtotal * $new_costo["factor"];
            $costos->$etiqueta = $new_valor;
          } else {
            $costos->$etiqueta = $new_costo['valor'];
          }
        }
        $costo_total = 0;
        foreach($costos as $costos_key=>$costos_value) {
            $costo_total += $costos_value;
        }
        $costos->TOTAL = $costo_total;
        try{
          DB::beginTransaction();
          $data = $request->json()->all();
          $response = $factura_payload->update([
            'valores'=>json_encode($costos),
          ]);
          DB::commit();
          return response()->json($costos,200);
        } catch (Exception $e) {
          return response()->json($e->getMessage(),400);
        }
    }
}
