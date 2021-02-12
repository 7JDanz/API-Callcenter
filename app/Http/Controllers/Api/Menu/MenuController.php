<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuAgrupacion;
use App\Models\MenuCategorias;
use App\Models\MenuPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Classes\MenuUtil;

class MenuController extends Controller
{

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

    public function menuPayload($pais,$menu,Request $request)
    {
        $menu_util = new MenuUtil();

        $restaurante = $request->IDRestaurante;
        $menuPayload = null;
        $plus_filter = '';
        $toReturn = [];
        if(!\Cache::has($menu))
        {
            $menuPayload = MenuPayload::where("IDMenu", $menu)
                                    ->where('status', '=', '1')
                                    ->get();
            \Cache::put($menu, $menuPayload, 3600);

            $plus_filter = $menu_util->get_productos_menu($menuPayload);

            \Cache::put('plus_'.$menu, $plus_filter, 3600);
        } else {
            $menuPayload = \Cache::get($menu);
            $plus_filter = \Cache::get('plus_'.$menu);
        }

        $sql_query = "select * from config.fn_buscaPreciosxPlu ($restaurante,'$plus_filter')";
        $precios = DB::connection($this->getConnectionName())->select($sql_query);
        $toReturn = $menu_util->get_productos($menuPayload,$precios);

        return response()->json(
            $toReturn
            , 200
            , ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8']
            ,JSON_PRETTY_PRINT
        );
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
            $this->menuPayload($pais,$menu,$request);
            return $this->buscarProducto($request,$pais,$menu);
        }
    }

    public function busqueda(Request $request,$menu){

        $menu_util = new MenuUtil();
        $restaurante = $request->IDRestaurante;//DEL request
        $menuPayload = json_decode(\Cache::get($menu),true);
        $menus = $menuPayload;
        $buscado = $request->descripcion;

        $productos_encontrados = $menu_util->get_busqueda_productos($menus,$buscado);

        $plus_filter = $menu_util->get_productos_encontrados($productos_encontrados);

        $sql_query = "select * from config.fn_buscaPreciosxPlu ($restaurante,'$plus_filter')";
        $precios = DB::connection($this->getConnectionName())->select($sql_query);
        $toReturn = $menu_util->process_productos($productos_encontrados, $precios);

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
        $request->request->add(['descripcion' => 'gratis']);
        //return $request;
        return $this->buscarProducto($request,$pais,$menu);
    }

    static function build_menu_cadena_updater($pais, $id_cadena) {
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
