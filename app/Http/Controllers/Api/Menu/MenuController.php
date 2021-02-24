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
use Illuminate\Support\Facades\Log;
Use Exception;
use stdClass;

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

    protected function get_menu_payload($menu) {
        $menu_util = new MenuUtil();
        $toReturn = [];
        $menuPayloads = MenuPayload::where("IDMenu", $menu)
                                    ->where('status', '=', '1')
                                    ->get();
        foreach($menuPayloads as $menuPayload) {
            $to_insert = new stdClass();
            $to_insert->IDMenu = $menuPayload->IDMenu;
            $to_insert->IDCadena = $menuPayload->IDCadena;
            $to_insert->MenuAgrupacion = $menu_util->build_menu_agrupacion($menuPayload->MenuAgrupacion);
            $to_insert->MenuCategorias = $menu_util->build_menu_categorias($menuPayload->MenuCategorias);
            $to_insert->status = $menuPayload->status;
            $to_insert->created_at = $menuPayload->created_at;
            $to_insert->updated_at = $menuPayload->updated_at;
            array_push($toReturn, $to_insert);
        }
        return $toReturn;
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
            $menuPayload = $this->get_menu_payload($menu);
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
        $menuPayload = \Cache::get($menu);
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
        $menuPayload = \Cache::get($menu);
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

    static function build_menu_cadena($connection) {
        $cadenas = DB::connection($connection)->select('SELECT DISTINCT IDCadena FROM trade.menu;');
        foreach($cadenas as $cadena) {
            try {
                $id_cadena = $cadena->IDCadena;
                $menus_en_cadena = DB::connection($connection)->table('trade.menu')->where("IDCadena", $id_cadena)->get();
                foreach($menus_en_cadena as $menu) {
                    $id_menu = $menu->IDMenu;
                    DB::connection($connection)->table('dbo.Menu_Payload')->where("IDMenu", $id_menu)->update(['status'=>0]);
                    $menu_agrupacion = DB::connection($connection)->table('callcenter.menu_productos_categoria')->where("IDMenu", $id_menu)->get();
                    $menu_categoria = DB::connection($connection)->table('callcenter.menu_productos_subcategoria')->where("IDMenu", $id_menu)->get();
                    $sql_insert = "INSERT INTO Menu_Payload ([IDMenu]
                    ,[IDCadena]
                    ,[MenuAgrupacion]
                    ,[MenuCategorias]
                    ,[status]
                    ,[created_at]
                    ,[updated_at]) VALUES (
                        :id_menu, :id_cadena, :menu_agrupacion, :menu_categoria, 1, GETDATE(), GETDATE()
                    );";
                    DB::connection($connection)->select($sql_insert,  [
                        'id_menu'=>$id_menu,
                        'id_cadena'=>$id_cadena,
                        'menu_agrupacion'=>json_encode($menu_agrupacion),
                        'menu_categoria'=>json_encode($menu_categoria),
                    ]);
                }
                Log::info("Construido Menu de IDCadena " . $cadena->IDCadena);
            } catch (Exception $e) {
                return $e->getMessage();
                Log::error("Fallo construcción del Menu de IDCadena " . $cadena->IDCadena);
            }
        }
        return "Construidos los Menu de la conexión ". $connection;
    }

    function build_menu_cadena_request(Request $request,$pais) {
        $id_cadena = $request['IDCadena'];
        $id_menu = $request['IDMenu'];
        $connection = $this->getConnectionName();
        DB::connection($connection)->table('dbo.Menu_Payload')->where("IDMenu", $id_menu)->update(['status'=>0]);
        $menu_agrupacion = DB::connection($connection)->table('callcenter.menu_productos_categoria')->where("IDMenu", $id_menu)->get();
        $menu_categoria = DB::connection($connection)->table('callcenter.menu_productos_subcategoria')->where("IDMenu", $id_menu)->get();
        $sql_insert = "INSERT INTO Menu_Payload ([IDMenu]
        ,[IDCadena]
        ,[MenuAgrupacion]
        ,[MenuCategorias]
        ,[status]
        ,[created_at]
        ,[updated_at]) VALUES (
            :id_menu, :id_cadena, :menu_agrupacion, :menu_categoria, 1, GETDATE(), GETDATE()
        );";
        DB::connection($connection)->select($sql_insert,  [
            'id_menu'=>$id_menu,
            'id_cadena'=>$id_cadena,
            'menu_agrupacion'=>json_encode($menu_agrupacion),
            'menu_categoria'=>json_encode($menu_categoria),
        ]);
        return response()->json("Construido Menu " .  $id_menu . "de IDCadena " . $id_cadena,200);
    }

    protected function getConnectionName()
    {
        return Config::get("NOMBRE_CONEXION_AZURE");
    }
}
