<?php
namespace App\Classes;
use stdClass;

class MenuUtil
{


    public function build_menu_agrupacion($menuAgrupacionList) {
        $toReturn = [];
        foreach($menuAgrupacionList as $menuAgrupacion) {
            $to_insert_menu_agrupacion = new stdClass();
            $to_insert_menu_agrupacion->IDMenu = $menuAgrupacion['IDMenu'];
            $to_insert_menu_agrupacion->IDCategoria = $menuAgrupacion['IDCategoria'];
            $to_insert_menu_agrupacion->categoria = $menuAgrupacion['categoria'];
            $to_insert_menu_agrupacion->productos = json_decode($menuAgrupacion['productos']);
            array_push($toReturn, $to_insert_menu_agrupacion);
        }
        return $toReturn;
    }

    public function build_menu_categorias($menuCategoriasList) {
        $toReturn = [];
        foreach($menuCategoriasList as $menuCategorias) {
            $to_insert_menu_categorias = new stdClass();
            $to_insert_menu_categorias->IDMenu = $menuCategorias['IDMenu'];
            $to_insert_menu_categorias->IDCategoria = $menuCategorias['IDCategoria'];
            $to_insert_menu_categorias->IDSubcategoria = $menuCategorias['IDSubcategoria'];
            $to_insert_menu_categorias->categoria = $menuCategorias['categoria'];
            $to_insert_menu_categorias->productos = json_decode($menuCategorias['productos']);
            array_push($toReturn, $to_insert_menu_categorias);
        }
        return $toReturn;
    }

    public function process_menu_agrupacion($menu_agrupacion, $precios) {
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

    public function process_menu_categorias($menu_categorias, $precios) {
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

    public function process_productos($productos, $precios) {
        $toReturn = [];
        $productos_to_process = json_decode(json_encode($productos), true);
        foreach ($productos_to_process as $producto) {
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
            $producto_to_insert['precio'] = $this->get_precio_producto($producto["IDProducto"], $precios);
            array_push($toReturn, $producto_to_insert);
        }
        return $toReturn;
    }

    public function process_Preguntas($preguntas, $precios) {
        $new_preguntas = [];
        $preguntas_to_process = json_decode(json_encode($preguntas), true);
        foreach($preguntas_to_process as $pregunta) {
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

    public function get_precio_producto($id_producto, $precios) {
        foreach ($precios as $precio) {
            if ($precio->IDProducto == $id_producto) {
                return [
                    "precioBruto"=>round($precio->precioBruto,2)
                ];
            }
        }
    }

    public function get_productos_menu($menuPayload){
        $plus = [];

        foreach ($menuPayload as $payload) {
            foreach($payload->MenuAgrupacion as $menu_agrupacion) {
                if (is_array($menu_agrupacion->productos)) {
                    foreach($menu_agrupacion->productos as $producto) {
                        array_push($plus, $producto->IDProducto);
                        if (is_array($producto->Preguntas)) {
                            foreach($producto->Preguntas as $pregunta) {
                                if (is_array($pregunta->Respuestas)) {
                                    foreach($pregunta->Respuestas as $respuesta) {
                                        array_push($plus, $respuesta->IDProducto);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return join(',',$plus);
    }

    public function get_productos($menuPayload,$precios){
        $toReturn = [];
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
        return $toReturn;
    }

    public function get_productos_encontrados($productos_encontrados){

        //Busqueda de Respuestas
        $plus = [];
        foreach ($productos_encontrados as $producto) {
            array_push($plus, $producto->IDProducto);
            if (is_array($producto->Preguntas)) {
                foreach($producto->Preguntas as $pregunta) {
                    if (is_array($pregunta->Respuestas)) {
                        foreach($pregunta->Respuestas as $respuesta) {
                            array_push($plus, $respuesta->IDProducto);
                        }
                    }
                }
            }
        }
        return implode(',',$plus);

    }

    public function get_busqueda_productos($menus,$buscado){
        //Busqueda de Menu Agrupacion

        $productos_encontrados = [];
        foreach ($menus as $item_menu) {

            foreach ($item_menu->MenuAgrupacion as $item_menu_agrupacion) {

                if(is_array($item_menu_agrupacion->productos) /*|| is_object($item_menu_agrupacion["productos"])*/)
                {
                    foreach ($item_menu_agrupacion->productos as $producto) {
                        $buscar_impresion = strpos( strtolower($producto->impresion), strtolower($buscado));
                        $buscar_descripcion = strpos( strtolower($producto->DescripcionProducto), strtolower($buscado));

                        if ($buscar_impresion !== false || $buscar_descripcion !== false) {
                            array_push($productos_encontrados, $producto);
                        }
                    }
                }
            }
        }
        return $productos_encontrados;
    }

    public function get_busqueda_producto_id($menus,$buscado){
        $productos_encontrados = [];
        foreach($buscado as $idproducto){
            foreach ($menus as $menu) {
                foreach ($menu->MenuAgrupacion as $item_menu_agrupacion) {
                    if(is_array($item_menu_agrupacion->productos)){
                        foreach ($item_menu_agrupacion->productos as $producto) {
                            if ($producto->IDProducto === $idproducto) {
                                array_push($productos_encontrados, $producto);
                            }
                        }
                    }
                }
            }
        }
        return $productos_encontrados;
    }
    
}
