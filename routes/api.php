<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Clientes\ClienteController;
use App\Http\Controllers\GeolocalizacionController;
use App\Http\Controllers\Api\Pais\PaisController;
use App\Http\Controllers\Api\Restaurante\RestauranteController;

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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::middleware('auth:api')->get('/usuario', function (Request $request) {
    return $request->user();
});

Route::name("v1.")->middleware('auth:api')->group(function(){
    Route::get('/cliente/{documento}',[ClienteController::class, 'cliente'] )->name('clientepordocumento');
    Route::get('/cliente-email/{email}',[ClienteController::class, 'clientePorEmail'] )->name('clienteporemail');
    Route::get('/cliente-telefono/{telefono}',[ClienteController::class, 'clientePorTelefono'] )->name('clienteportelefono');

    Route::get( '/v1/ecu/geolocalizacion' , [GeolocalizacionController::class,'index'] );
    Route::post( '/v1/ecu/geolocalizacion' , [GeolocalizacionController::class,'store'] );
    Route::get( '/v1/ecu/geolocalizacion/{id}' , [GeolocalizacionController::class,'show'] );
    Route::put( '/v1/ecu/geolocalizacion/{id}' , [GeolocalizacionController::class,'update'] );
    Route::delete( '/v1/ecu/geolocalizacion/{id}' , [GeolocalizacionController::class,'destroy'] );
});

// Route::resource( '/v1/ecu/geolocalizacion' , GeolocalizacionController::class);
Route::get('/pais',[PaisController::class,'index']);
Route::get('/restaurante/IDRestaurante/{id}',[RestauranteController::class,'restaurantePorId'])->name('id');
Route::get('/restaurante/IDCadena/{id}',[RestauranteController::class,'restaurantePorCadena'])->name('restaurante');
Route::get('/restaurante/poligono-cobertura',[RestauranteController::class,'poligonoCobertura'])->name('poligonoCobertura');

Route::get( '/v1/ecu/buscar-restaurante-cercano' , [GeolocalizacionController::class,'getCercania']);
Route::get( '/v1/ecu/datos-restaurante' , [GeolocalizacionController::class,'getDatosRestaurante']);


