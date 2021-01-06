<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuAgrupacion;
use App\Models\MenuCategorias;
use App\Models\MenuPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    public function menuPorCadena($cadena)
    {
        $myArray = explode(',', $cadena);        
        $menu = Menu::whereIn("IDCadena", $myArray)->get();
        return response()->json([
            'Menus' => $menu
        ]);
    }

    public function menuAgrupadoPorid($menu)
    {            
        $menuAgrupado = MenuAgrupacion::where("IDMenu", $menu)->get();         
        return $menuAgrupado;

    }

    public function menuPayload($menu)
    {
        if(!\Cache::has($menu))
        {
            $menuPayload = MenuPayload::where("IDMenu", $menu)
                                    ->where('status', '=', '1')
                                    ->get(); 
            //return  $menuPayload;
            \Cache::put($menu, $menuPayload, 3600);
           
        }
        return \Cache::get($menu);
    }


    public function menuCategorias($menu)
    {
        $menuCategoria = MenuCategorias::where("IDMenu", $menu)                                        
                                        ->get();
        return  $menuCategoria;                                   
    }


    public function buscarProducto(Request $request,$menu)
    {        
        $menuPayload = json_decode(\Cache::get($menu),true);
        if($menuPayload)
        {
            return $this->busqueda($request,$menu);

        }else{            
            $this->menuPayload($menu);
            return $this->buscarProducto($request,$menu);
        }
    }
    
    public function busqueda(Request $request,$menu){
        
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
                      
           return response()->json(
                $productos_encontrados
                , 200
                , ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8']
                ,JSON_PRETTY_PRINT
           );

    }


}
