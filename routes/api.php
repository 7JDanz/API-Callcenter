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
    Route::post('/actualizar_usuarios' , [UsuariosPosController::class,'actualizar_usuarios'] );
    Route::post('/update_users_batch' , [UsuariosPosController::class,'update_users_batch'] );
    Route::get('/pais',[PaisController::class,'index']);
});

//Route::name("v1.")->middleware(['multipais', 'auth:api'])->prefix("/{pais}")
Route::middleware(['multipais', 'auth:api'])->prefix("/{pais}")
->where(['pais' => 'ecu|chi|col|arg'])
->group(function(){
    Route::get('/prueba' , function (Request $request) { return 1; });
    Route::get('/pruebamenu' , [MenuController::class,'prueba_menu']);

    //CLIENTES
    Route::get('/cliente/{documento}' , [ClienteController::class, 'cliente'] )->name('clientepordocumento');
    Route::get('/cliente-email/{email}' , [ClienteController::class, 'clientePorEmail'] )->name('clienteporemail');
    Route::get('/cliente-telefono/{telefono}' , [ClienteController::class, 'clientePorTelefono'] )->name('clienteportelefono');

    //GEOLOCALIZACION
    Route::get('/geolocalizacion' , [GeolocalizacionController::class,'index'] );
    Route::post('/geolocalizacion' , [GeolocalizacionController::class,'store'] );
    Route::get('/geolocalizacion/{id}' , [GeolocalizacionController::class,'show'] );
    Route::put('/geolocalizacion/{id}' , [GeolocalizacionController::class,'update'] );
    Route::delete('/geolocalizacion/{id}' , [GeolocalizacionController::class,'destroy'] );

    //RESTAURANTE
    Route::get('/restaurante/IDRestaurante/{id}' , [RestauranteController::class,'restaurantePorId'])->name('id');
    Route::get('/restaurante/IDCadena/{id}' , [RestauranteController::class,'restaurantePorCadena'])->name('restaurante');
    Route::get('/restaurante/poligono-cobertura' , [RestauranteController::class,'poligonoCobertura'])->name('poligonoCobertura');
    Route::get('/buscar-restaurante-cercano' , [GeolocalizacionController::class,'getCercania']);
    Route::get('/datos-restaurante' , [GeolocalizacionController::class,'getDatosRestaurante']);

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
});








Route::get( '/v1/ecu/restaurantes-cercanos' , [GeolocalizacionController::class,'getRestaurantesCercanos']);
Route::get( '/v1/ecu/obtener-puntos-geo' , [GeolocalizacionController::class,'obtenerPuntos']);
