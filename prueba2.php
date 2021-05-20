<?php
$menuPayload = json_decode(file_get_contents("./menu.json"));

$plus = [];

foreach ($menuPayload as $payload) {
    foreach($payload->MenuAgrupacion as $menu_agrupacion) {
        if (is_array($menu_agrupacion->productos)) {
            foreach($menu_agrupacion->productos as $producto) {
                array_push($plus, $producto->IDProducto);
                if (isset($pregunta->Respuestas)) {
                    if (is_array($producto->Preguntas)) {
                        foreach($producto->Preguntas as $pregunta) {
                            if (isset($pregunta->Respuestas)) {
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
    }
}
echo json_encode($plus);
