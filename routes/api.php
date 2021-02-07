<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Clientes\ClienteController;
use App\Http\Controllers\GeolocalizacionController;
use App\Http\Controllers\Api\Pais\PaisController;
use App\Http\Controllers\Api\Restaurante\RestauranteController;
use App\Http\Controllers\Api\Menu\MenuController;
use App\Http\Controllers\Api\Menu\SubcategoriaController;
use App\Http\Controllers\Api\Usuarios\UsuariosPosController;
use App\Http\Controllers\Api\FacturaPayload\FacturaPayloadController;
use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['multipais'])
        ->prefix("/{pais}")
        ->where(['pais' => 'ecu|chi|col|arg'])
        ->group(function() {
            Route::post('/login' , [UsuariosPosController::class,'validarDatosAcceso'] );
});

Route::middleware([])->group(function() {
    Route::get('/get_codigo_app' , function (Request $request) {
        $codigoAplicacion=config("app.bi.codigo_aplicacion");
        if(is_null($codigoAplicacion)) {
            Log::error("Falta la configuracion BI_CODIGO_APLICACION en el archivo .env");
            throw new \Exception("Revisar log de la aplicación");
        }
        return json_encode([
            "codigoApp"=>$codigoAplicacion
        ]);
    });
    Route::post('/actualizar_usuarios' , [UsuariosPosController::class,'actualizar_usuarios'] );
    Route::post('/update_users_batch' , [UsuariosPosController::class,'update_users_batch'] );
    Route::get('/pais',[PaisController::class,'index']);
});

//Route::name("v1.")->middleware(['multipais', 'auth:api'])->prefix("/{pais}")
Route::middleware(['multipais', 'auth:api'])->prefix("/{pais}")
->where(['pais' => 'ecu|chi|col|arg'])
->group(function(){
    Route::get('/pruebamenu' , [MenuController::class,'prueba_menu']);

    //FACTURA
    Route::get('/facturapayload' , [FacturaPayloadController::class, 'get']);
    Route::post('/facturapayload' , [FacturaPayloadController::class, 'post']);
    Route::put('/facturapayload' , [FacturaPayloadController::class, 'put']);
    Route::put('/facturapayload/put_detalle' , [FacturaPayloadController::class, 'put_detalle']);
    Route::put('/facturapayload/put_formas_pago' , [FacturaPayloadController::class, 'put_formasPago']);
    Route::put('/facturapayload/put_cabecera' , [FacturaPayloadController::class, 'put_cabecera']);
    Route::delete('/facturapayload' , [FacturaPayloadController::class, 'delete']);
    Route::post('/facturapayload/inserta_producto' , [FacturaPayloadController::class, 'inserta_producto']);
    Route::post('/facturapayload/inserta_varios_productos' , [FacturaPayloadController::class, 'inserta_varios_producto']);
    Route::post('/facturapayload/borra_producto' , [FacturaPayloadController::class, 'borra_producto']);
    Route::post('/facturapayload/borra_varios_productos' , [FacturaPayloadController::class, 'borra_varios_producto']);

    //CLIENTES
    Route::get('/cliente/{documento}' , [ClienteController::class, 'cliente'] )->name('clientepordocumento');
    Route::get('/cliente-email/{email}' , [ClienteController::class, 'clientePorEmail'] )->name('clienteporemail');
    Route::get('/cliente-telefono/{telefono}' , [ClienteController::class, 'clientePorTelefono'] )->name('clienteportelefono');

    //GEOLOCALIZACION
    Route::get('/geolocalizacion' ,  [GeolocalizacionController::class,'index']);
    Route::post('/geolocalizacion' , [GeolocalizacionController::class,'store'] );
    Route::get( '/geolocalizacion/{id}' , [GeolocalizacionController::class,'show']);
    Route::put('/geolocalizacion/{id}' , [GeolocalizacionController::class,'update'] );
    Route::delete('/geolocalizacion/{id}' , [GeolocalizacionController::class,'destroy'] );


    //RESTAURANTE
    Route::get('/restaurante/IDRestaurante/{id}' , [RestauranteController::class,'restaurantePorId'])->name('id');
    Route::get('/restaurante/IDCadena/{id}' , [RestauranteController::class,'restaurantePorCadena'])->name('restaurante');
    Route::get('/restaurante/poligono-cobertura' , [RestauranteController::class,'poligonoCobertura'])->name('poligonoCobertura');
   // Route::get('/buscar-restaurante-cercano' , [GeolocalizacionController::class,'getCercania']);
    Route::get( '/restaurantes-cercanos' , [GeolocalizacionController::class,'getRestaurantesCercanos']);
    Route::get( '/obtener-puntos-geo' ,    [GeolocalizacionController::class,'obtenerPuntos']);
    Route::get('/datos-restaurante' ,      [GeolocalizacionController::class,'getDatosRestaurante']);

    //Menu por Cadena
    Route::get('/menu/IDCadena/{id}',[MenuController::class,'menuPorCadena'])->name('menuPorCadena');
    //Busqueda por ID
    Route::get('/menu/IDMenu/{id}',[MenuController::class,'menuAgrupadoPorid'])->name('MenuPorId');
    //Menu categorias
    Route::get('/menu/menu-categoria/IDMenu/{id}',[MenuController::class,'menuCategorias'])->name('MenuPorCategoria');;
    //Menu agrupacion
    Route::get('/menu/menu-agrupacion/IDMenu/{id}',[MenuController::class,'menuPayload'])->name('MenuAgrupacionPorId');
    //Buscar producto
    Route::get('/menu/menu-buscar/IDMenu/{id}',[MenuController::class,'buscarProducto'])->name('MenuBuscar');
    //Buscar Subcategoria
    Route::get('/menu/subcategoria/IDMenu/{id}',[SubcategoriaController::class,'index'])->name('MenuSubcategoria');

    //Producto Upselling
    Route::get('/menu/upselling',[MenuController::class,'upselling'])->name('Upselling');

    Route::get('/menu/build_menu_cadena/IDCadena/{id}',[MenuController::class,'build_menu_cadena']);
});



   Route::get( '/v1/ecu/prueba' , [GeolocalizacionController::class,'prueba']);
 //Route::get('/datos-restaurante' , [GeolocalizacionController::class,'getDatosRestaurante']);
