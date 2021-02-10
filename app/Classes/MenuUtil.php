<?php
namespace App\Classes;

class MenuUtil
{
    const PRODUCTOS     = "productos";
    const IDPRODUCTO    = "IDProducto";
    const PREGUNTAS     = "Preguntas";
    const RESPUESTAS    = "Respuestas";

    public function process_menu_agrupacion($menu_agrupacion, $precios) {
        $toReturn = [];
        foreach ($menu_agrupacion as $item) {
            $new_item = null;
            foreach ($item as $key=>$value) {
                if ($key == self::PRODUCTOS) {
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
                if ($key == self::PRODUCTOS) {
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
        foreach ($productos as $producto) {
            $producto_to_insert = null;
            foreach ($producto as $key => $value) {
                if ($key == self::PREGUNTAS) {
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
            $producto_to_insert['precio'] = $this->get_precio_producto($producto[self::IDPRODUCTO], $precios);
            array_push($toReturn, $producto_to_insert);
        }
        return $toReturn;
    }

    public function process_Preguntas($preguntas, $precios) {
        $new_preguntas = [];
        foreach($preguntas as $pregunta) {
            $new_pregunta = null;
            foreach($pregunta as $key_pregunta=>$value_pregunta) {
                if ($key_pregunta == self::RESPUESTAS) {
                    $new_respuestas = null;
                    if (is_array($value_pregunta)) {
                        $new_respuestas = [];
                        $respuestas = $value_pregunta;
                        foreach($respuestas as $respuesta) {
                            $new_respuesta = null;
                            foreach($respuesta as $key_respuesta=>$value_respuesta) {
                                $new_respuesta[$key_respuesta] = $value_respuesta;
                                if ($key_respuesta == self::IDPRODUCTO) {
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
                if (is_array($menu_agrupacion[self::PRODUCTOS])) {
                    foreach($menu_agrupacion[self::PRODUCTOS] as $producto) {
                        array_push($plus, $producto[self::IDPRODUCTO]);
                        if (is_array($producto[self::PREGUNTAS])) {
                            foreach($producto[self::PREGUNTAS] as $pregunta) {
                                if (is_array($pregunta[self::RESPUESTAS])) {
                                    foreach($pregunta[self::RESPUESTAS] as $respuesta) {
                                        array_push($plus, $respuesta[self::IDPRODUCTO]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return implode(',',$plus);
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
            array_push($plus, $producto[self::IDPRODUCTO]);
            if (is_array($producto[self::PREGUNTAS])) {
                foreach($producto[self::PREGUNTAS] as $pregunta) {
                    if (is_array($pregunta[self::RESPUESTAS])) {
                        foreach($pregunta[self::RESPUESTAS] as $respuesta) {
                            array_push($plus, $respuesta[self::IDPRODUCTO]);
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

            foreach ($item_menu['MenuAgrupacion'] as $item_menu_agrupacion) {

                if(is_array($item_menu_agrupacion[self::PRODUCTOS]) /*|| is_object($item_menu_agrupacion[self::PRODUCTOS])*/)
                {
                    foreach ($item_menu_agrupacion[self::PRODUCTOS] as $producto) {
                        $buscar_impresion = strpos( strtolower($producto['impresion']), strtolower($buscado));
                        $buscar_descripcion = strpos( strtolower($producto['DescripcionProducto']), strtolower($buscado));

                        if ($buscar_impresion !== false || $buscar_descripcion !== false) {
                            array_push($productos_encontrados, $producto);
                        }
                    }
                }
            }
        }
        return $productos_encontrados;
    }

}
