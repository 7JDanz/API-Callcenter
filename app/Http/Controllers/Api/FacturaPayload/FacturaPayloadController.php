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
        $id_restaurante = $request['IDRestaurante'];
        $id_cadena = $request['IDCadena'];
        if ($id == null) {
           $factura_payloads = FacturaPayload::get();
           $toReturn = [];
           foreach($factura_payloads as $factura_payload) {
               $factura_payload->detalle = json_decode($factura_payload->detalle);
               $factura_payload->modificadores = json_decode($factura_payload->modificadores);
               $factura_payload->cabecera = json_decode($factura_payload->cabecera);
               $factura_payload->valores = json_decode($factura_payload->valores);
               array_push($toReturn, $factura_payload);
           }
           return response()->json($toReturn,200);
        } else {
           $factura_payload = FacturaPayload::where('IDCadena', $id_cadena)->where('IDRestaurante', $id_restaurante)->where('IDFactura', $id)->first();
           if ($factura_payload) {
            $factura_payload->detalle = json_decode($factura_payload->detalle);
            $factura_payload->modificadores = json_decode($factura_payload->modificadores);
            $factura_payload->cabecera = json_decode($factura_payload->cabecera);
            $factura_payload->valores = json_decode($factura_payload->valores);
           }
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
        $id_restaurante = $data['IDRestaurante'];
        $id_cadena = $data['IDCadena'];
        $new_factura_payload = new FacturaPayload();
        $new_factura_payload->detalle = json_encode($data['detalle']);
        $new_factura_payload->modificadores = json_encode($data['modificadores']);
        $new_factura_payload->cabecera = json_encode($data['cabecera']);
        $new_factura_payload->valores = json_encode($data['valores']);
        $new_factura_payload->status = 'activo';
        $new_factura_payload->IDFactura = $new_id_factura;
        $new_factura_payload->IDRestaurante = $id_restaurante;
        $new_factura_payload->IDCadena = $id_cadena;
        $new_factura_payload->save();
        return response()->json($new_id_factura,200);
    }

    public function put(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->update([
               'detalle'=>json_encode($data['detalle']),
               'modificadores'=>json_encode($data['modificadores']),
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
            $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->update([
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
            $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->update([
                'valores'=>json_encode($data['valores']),
            ]);
            DB::commit();
            return response()->json(true,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function put_detalle(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $data = $request->json()->all();
            $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->update([
                'detalle'=>json_encode($data['detalle']),
                'modificadores'=>json_encode($data['modificadores']),
            ]);
            DB::commit();
            return response()->json(true,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function delete(Request $request, $pais) {
        try{
            DB::beginTransaction();
            $factura_payload = FacturaPayload::where('IDCadena', $request['IDCadena'])->where('IDRestaurante', $request['IDRestaurante'])->where('IDFactura', $request['IDFactura'])->update([
                'status'=>'inactivo',
            ]);
            DB::commit();
            return response()->json(true,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function inserta_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->first();
        $detalle = json_decode($factura_payload->detalle);
        $modificadores = json_decode($factura_payload->modificadores);
        $new_producto = $data['producto'];
        $codModificador = $data['codModificador'];
        $cantidad = $data['cantidad'];
        $detalleApp =  uniqid();
        $item = new stdClass();
        $item->detalleApp = $detalleApp;
        $item->codPlu = $new_producto['codPlu'];
        $item->precioBruto = $new_producto['precioBruto'];
        $item->cantidad = $cantidad;
        array_push($detalle, $item);

        $item_modificador = new stdClass();
        $item_modificador->detalleApp = $detalleApp;
        $item_modificador->codModificador = $codModificador;
        array_push($modificadores, $item_modificador);

        try{
            DB::beginTransaction();
            $factura_payload->update([
                'detalle'=>json_encode($detalle),
                'modificadores'=>json_encode($modificadores),
            ]);
            DB::commit();
            return response()->json($detalle,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }


    public function inserta_varios_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->first();
        $detalle = json_decode($factura_payload->detalle);
        $modificadores = json_decode($factura_payload->modificadores);
        $items = $data['items'];
        foreach($items as $item) {
            $new_producto = $item['producto'];
            $codModificador = $item['codModificador'];
            $cantidad = $item['cantidad'];
            $detalleApp =  uniqid();
            $new_item_detalle = new stdClass();
            $new_item_detalle->detalleApp = $detalleApp;
            $new_item_detalle->codPlu = $new_producto['codPlu'];
            $new_item_detalle->precioBruto = $new_producto['precioBruto'];
            $new_item_detalle->cantidad = $cantidad;
            array_push($detalle, $new_item_detalle);

            $new_item_modificador = new stdClass();
            $new_item_modificador->detalleApp = $detalleApp;
            $new_item_modificador->codModificador = $codModificador;
            array_push($modificadores, $new_item_modificador);
        }
        try{
            DB::beginTransaction();
            $factura_payload->update([
                'detalle'=>json_encode($detalle),
                'modificadores'=>json_encode($modificadores),
            ]);
            DB::commit();
            return response()->json($detalle,200);
        } catch (Exception $e) {
            return response()->json($e,400);
        }
    }

    public function borra_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->first();
        $detalle = json_decode($factura_payload->detalle);
        $modificadores = json_decode($factura_payload->modificadores);
        $detalleApp = $data['detalleApp'];
        $new_detalle = [];
        $new_modificadores = [];
        $eliminado = false;
        foreach($detalle as $item) {
            $detalle_item = (object) $item;
            if ($detalle_item->detalleApp == $detalleApp) {
                $eliminado = true;
            } else {
                array_push($new_detalle, $item);
            }
        }
        foreach($modificadores as $item) {
            $modificador_item = (object) $item;
            if ($modificador_item->detalleApp == $detalleApp) {
                $eliminado = true;
            } else {
                array_push($new_modificadores, $item);
            }
        }
        if ($eliminado) {
            try{
                DB::beginTransaction();
                $factura_payload->update([
                    'detalle'=>json_encode($new_detalle),
                    'modificadores'=>json_encode($modificadores),
                ]);
                DB::commit();
                return response()->json(["detalle"=>$new_detalle,"modificadores"=>$new_modificadores],200);
            } catch (Exception $e) {
                return response()->json($e,400);
            }
        } else {
            return response()->json("producto no encontrado", 400);
        }
    }

    public function borra_varios_producto(Request $request, $pais) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->first();
        $detalle = json_decode($factura_payload->detalle);
        $ids_producto_borrar = $data['ids'];
        $new_detalle = [];
        $eliminados = false;
        foreach($detalle as $item) {
            $eliminado = false;
            $detalle_item = (object) $item;
            foreach($ids_producto_borrar as $id_producto_borrar) {
                if ($detalle_item->id == $id_producto_borrar) {
                    $eliminados = true;
                    $eliminado = true;
                }
            }
            if (!$eliminado) {
                array_push($new_detalle, $item);
            }
        }
        if (!$eliminados) {
            return response()->json("no se eliminaron productos", 400);
        } else {
            try{
                DB::beginTransaction();
                $factura_payload->update([
                    'detalle'=>json_encode($new_detalle),
                ]);
                DB::commit();
                return response()->json($new_detalle,200);
            } catch (Exception $e) {
                return response()->json($e,400);
            }
        }
    }

    public function calcula_valores(Request $request) {
        $data = $request->json()->all();
        $factura_payload = FacturaPayload::where('IDCadena', $data['IDCadena'])->where('IDRestaurante', $data['IDRestaurante'])->where('IDFactura', $data['IDFactura'])->first();
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
