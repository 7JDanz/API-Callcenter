<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuAgrupacion;
use App\Models\MenuCategorias;
use App\Models\MenuPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class MenuController extends Controller
{
    /**
     * @OA\Get(
     *      path="/menu/IDCadena/{IDCadena}",
     *      operationId="getMenuPorIDCadena",
     *      tags={"Menu"},
     *      summary="Lista menu por IDCadena",
     *      description="Retorna menu por IDCadena",
     *
     *      @OA\Parameter(
     *          description="Puede buscar por uno ó varios Ids separados por coma /10 ó /12,10",
     *          name="IDCadena",
     *          in="path",
     *          required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function menuPorCadena($pais,$cadena)
    {
        $myArray = explode(',', $cadena);
        $menu = Menu::whereIn("IDCadena", $myArray)->get();
        return response()->json([
            'Menus' => $menu
        ]);
    }

    public function menuAgrupadoPorid($pais,$menu)
    {
        $menuAgrupado = MenuAgrupacion::where("IDMenu", $menu)->get();
        return $menuAgrupado;

    }

    public function menuPayload($pais,$menu)
    {
        $restaurante = 40;
        $menuPayload = null;
        $plus_filter = '';
        if(!\Cache::has($menu))
        {
            $menuPayload = MenuPayload::where("IDMenu", $menu)
                                    ->where('status', '=', '1')
                                    ->get();
            \Cache::put($menu, $menuPayload, 3600);

            $plus = [];
            foreach ($menuPayload as $payload) {
                foreach($payload->MenuAgrupacion as $menu_agrupacion) {
                    if (is_array($menu_agrupacion['productos'])) {
                        foreach($menu_agrupacion['productos'] as $producto) {
                            array_push($plus, $producto['IDProducto']);
                            if (is_array($producto['Preguntas'])) {
                                foreach($producto['Preguntas'] as $pregunta) {
                                    if (is_array($pregunta['Respuestas'])) {
                                        foreach($pregunta['Respuestas'] as $respuesta) {
                                            array_push($plus, $respuesta['IDProducto']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $plus_filter = implode(',',$plus);

            \Cache::put('plus_'.$menu, $plus_filter, 3600);
        } else {
            $menuPayload = \Cache::get($menu);
            $plus_filter = \Cache::get('plus_'.$menu);
        }

        $toReturn = [];
        $sql_query = "select * from config.fn_buscaPreciosxPlu ($restaurante,'$plus_filter')";
        $precios = DB::connection($this->getConnectionName())->select($sql_query);
        foreach ($menuPayload as $payload) {
            $new_item_to_return = null;
            $new_payload = json_decode(json_encode($payload),true);
            foreach($new_payload as $key=>$value) {
                if ($key ==  "MenuAgrupacion") {
                    $new_item_to_return[$key] = $this->process_menu_agrupacion($value,$precios);
                } elseif ($key ==  "MenuCategorias") {
                    $new_item_to_return[$key] = $this->process_menu_categorias($value,$precios);
                } else {
                    $new_item_to_return[$key] = $value;
                }
            }
            array_push($toReturn, $new_item_to_return);
        }
        return response()->json(
            $toReturn
            , 200
            , ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8']
            ,JSON_PRETTY_PRINT
        );
    }

    function process_menu_agrupacion($menu_agrupacion, $precios) {
        $toReturn = [];
        foreach ($menu_agrupacion as $item) {
            $new_item = null;
            foreach ($item as $key=>$value) {
                if ($key == "productos") {
                    $new_productos = null;
                    if (is_array($value)) {
                        $new_productos = $this->process_productos($value, $precios);
                    }
                    $new_item[$key] = $new_productos;
                } else {
                    $new_item[$key] = $value;
                }
            }
            array_push($toReturn, $new_item);
        }
        return $toReturn;
    }

    function process_menu_categorias($menu_categorias, $precios) {
        $toReturn = [];
        foreach ($menu_categorias as $item) {
            $new_item = null;
            foreach ($item as $key=>$value) {
                if ($key == "productos") {
                    $new_productos = null;
                    if (is_array($value)) {
                        $new_productos = $this->process_productos($value, $precios);
                    }
                    $new_item[$key] = $new_productos;
                } else {
                    $new_item[$key] = $value;
                }
            }
            array_push($toReturn, $new_item);
        }
        return $toReturn;
    }

    public function menuCategorias($pais,$menu)
    {
        $menuCategoria = MenuCategorias::where("IDMenu", $menu)
                                        ->get();
        return  $menuCategoria;
    }


    public function buscarProducto(Request $request,$pais,$menu)
    {
        $menuPayload = json_decode(\Cache::get($menu),true);
        if($menuPayload)
        {
            return $this->busqueda($request,$menu);

        }else{
            $this->menuPayload($pais,$menu);
            return $this->buscarProducto($request,$pais,$menu);
        }
    }

    public function busqueda(Request $request,$menu){
        $restaurante = 40;//DEL request
        $menuPayload = json_decode(\Cache::get($menu),true);
        $menus = $menuPayload;
        $buscado = $request->descripcion;
        $productos_encontrados = [];
        foreach ($menus as $item_menu) {
            foreach ($item_menu['MenuAgrupacion'] as $item_menu_agrupacion) {
                if(is_array($item_menu_agrupacion['productos']) || is_object($item_menu_agrupacion['productos']))
                {
                    foreach ($item_menu_agrupacion['productos'] as $producto) {
                        $buscar_impresion = strpos( strtolower($producto['impresion']), strtolower($buscado));
                        $buscar_descripcion = strpos( strtolower($producto['DescripcionProducto']), strtolower($buscado));
                        if ($buscar_impresion !== false || $buscar_descripcion !== false) {
                            array_push($productos_encontrados, $producto);
                        }
                    }
                }
            }
        }
        $plus = [];
        foreach ($productos_encontrados as $producto) {
            array_push($plus, $producto['IDProducto']);
            if (is_array($producto['Preguntas'])) {
                foreach($producto['Preguntas'] as $pregunta) {
                    if (is_array($pregunta['Respuestas'])) {
                        foreach($pregunta['Respuestas'] as $respuesta) {
                            array_push($plus, $respuesta['IDProducto']);
                        }
                    }
                }
            }
        }
        $plus_filter = implode(',',$plus);
        $sql_query = "select * from config.fn_buscaPreciosxPlu ($restaurante,'$plus_filter')";
        $precios = DB::connection($this->getConnectionName())->select($sql_query);
        $toReturn = $this->process_productos($productos_encontrados, $precios);
        return response()->json(
            $toReturn
            , 200
            , ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8']
            ,JSON_PRETTY_PRINT
        );

    }

    function upselling(Request $request,$pais){

        $plus_id = '';///del request
        $menu = "38D97934-A4B4-E911-80E2-000D3A019254";
        $request->request->add(['descripcion' => 'mini manicho']);
        //return $request;
        return $this->buscarProducto($request,$pais,$menu);
    }

    function process_productos($productos, $precios) {
        $toReturn = [];
        foreach ($productos as $producto) {
            $producto_to_insert = null;
            foreach ($producto as $key => $value) {
                if ($key == "Preguntas") {
                    $new_preguntas = null;
                    if (is_array($value)) {
                        $new_preguntas = $this->process_Preguntas($value, $precios);
                    } else {
                        $new_preguntas = $value;
                    }
                    $producto_to_insert[$key] = $new_preguntas;
                } else {
                    $producto_to_insert[$key] = $value;
                }
            }
            $producto_to_insert['precio'] = $this->get_precio_producto($producto['IDProducto'], $precios);
            array_push($toReturn, $producto_to_insert);
        }
        return $toReturn;
    }

    function process_Preguntas($preguntas, $precios) {
        $new_preguntas = [];
        foreach($preguntas as $pregunta) {
            $new_pregunta = null;
            foreach($pregunta as $key_pregunta=>$value_pregunta) {
                if ($key_pregunta == "Respuestas") {
                    $new_respuestas = null;
                    if (is_array($value_pregunta)) {
                        $new_respuestas = [];
                        $respuestas = $value_pregunta;
                        foreach($respuestas as $respuesta) {
                            $new_respuesta = null;
                            foreach($respuesta as $key_respuesta=>$value_respuesta) {
                                $new_respuesta[$key_respuesta] = $value_respuesta;
                                if ($key_respuesta == "IDProducto") {
                                    $new_respuesta['precio'] = $this->get_precio_producto($value_respuesta, $precios);
                                }
                            }
                            array_push($new_respuestas, $new_respuesta);
                        }
                    }
                    $new_pregunta[$key_pregunta]  = $new_respuestas;
                } else {
                    $new_pregunta[$key_pregunta]  = $value_pregunta;
                }
            }
            array_push($new_preguntas, $new_pregunta);
        }
        return $new_preguntas;
    }

    function get_precio_producto($id_producto, $precios) {
        foreach ($precios as $precio) {
            if ($precio->IDProducto == $id_producto) {
                return [
                    "iva"=>$precio->iva,
                    "precioNeto"=>$precio->precioNeto,
                    "precioBruto"=>$precio->precioBruto
                ];
            }
        }
    }

    function build_menu_cadena(Request $request, $pais, $id_cadena) {
        $menus_en_cadena = Menu::where("IDCadena", $id_cadena)->get();
        foreach($menus_en_cadena as $menu) {
            $id_menu = $menu->IDMenu;
            $menu_agrupacion = MenuAgrupacion::where("IDMenu", $id_menu)->get();
            $menu_categoria = MenuCategorias::where("IDMenu", $id_menu)->get();
            $insertado = true;
            try{
                $new_menu_payload = new MenuPayload();
                $new_menu_payload->IDMenu = $id_menu;
                $new_menu_payload->IDCadena = $id_cadena;
                $new_menu_payload->MenuAgrupacion = $menu_agrupacion;
                $new_menu_payload->MenuCategorias = $menu_categoria;
                $new_menu_payload->status = 1;
                $new_menu_payload->save();
            } catch (Exception $e) {
                $insertado = false;
            }
            if ($insertado) {
                try{
                    $preview_menu_payload = MenuPayload::where("IDMenu", $id_menu)->update([
                        'status'=>2,
                    ]);
                } catch (Exception $e) {
                    //ignored
                }
            }
        }
        return response()->json(["message"=>"builded"],200);
    }

    protected function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }
}
