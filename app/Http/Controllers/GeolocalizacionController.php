<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geolocalizacion;
use App\Models\HorarioAtencionRestaurante;
use App\Models\Locales;
use App\Models\Restaurante;
use Location\Coordinate;
use Location\Polygon;


class GeolocalizacionController extends Controller
{
    public function index()
    {
        return Geolocalizacion::all();
    }

    public function store(Request $request)
    {

        $geolocalizacion = Geolocalizacion::create($request->all());
        return $geolocalizacion;
    }

    public function show($id)
    {
        return Geolocalizacion::find($id);
    }

    public function update(Request $request, $id)
    {
        return   Geolocalizacion::find($id)->update($request->all());
    }

    public function destroy($id)
    {
        Geolocalizacion::findOrFail($id)->delete();
    }

    public function getRestaurantesCercanos(Request $request)
    {
        $c =  Locales::with(array('restaurante.horariosAtencion'=>function($query){
             $query->select( 'IDRestaurante' , 'Dia' , 'horaInicio' ,'horaFin');
       })) ;

       // $c = Locales::with("restaurante.horariosAtencion:IDRestaurante,IDTienda,Nombre");
        //->with(['restaurante.horariosAtencion']);
        $c->where('location.point', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    $request->longitud,
                    $request->latitud,
                ],
            ],
            '$maxDistance' => 5 * 1000,
        ]) ;
        return $c->get() ;
      //  return     $c->get()->makeHidden(['nombre','restaurante.horarios_atencion']) ;
    }


    public function obtenerPuntos(Request $request) {

        $address = urlencode($request->direccion);
        $googleMapUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyAgo_FfLGAjc0uszGFn2Za_Dssold8k_HM";
        $geocodeResponseData = file_get_contents($googleMapUrl);
        $responseData = json_decode($geocodeResponseData, true);

        if($responseData['status']=='OK') {
            $latitude = isset($responseData['results'][0]['geometry']['location']['lat']) ? $responseData['results'][0]['geometry']['location']['lat'] : "";
            $longitude = isset($responseData['results'][0]['geometry']['location']['lng']) ? $responseData['results'][0]['geometry']['location']['lng'] : "";
            $formattedAddress = isset($responseData['results'][0]['formatted_address']) ? $responseData['results'][0]['formatted_address'] : "";

            if($latitude && $longitude && $formattedAddress) {

                return ["direccion"  =>  $formattedAddress  ,"latitud" => $latitude ,  "longitud" => $longitude];
            }
            else {
                    return false;
                 }
            } else
            {
                echo "ERROR: {$responseData['status']}";
                return false;
            }
    }

    public function utest(Request $request)
    {
      return   Locales::with(array('restaurante.horariosAtencion'=>function($query){
            $query->select( 'IDRestaurante' , 'Dia' , 'horaInicio' ,'horaFin');
        }))->get();

        return  Locales::find("5ffe00f90c6d5852a7604792")
        ->with(['restaurante.horariosAtencion'])
        //->with(['subscriptionInvoiceDetails.store.city'])
        ->first() ;
        $c = Locales::with("restaurante.horariosAtencion");
        return  $c->pluck("nombre")  ;

    }

    public function getDatosRestaurante(Request $request)
    {
        $restaurante = Restaurante::where("IDRestaurante", $request->IDRestaurante)
        ->first();
        // $restaurante = Restaurante::with(['geolocalizacion'])
        //                             ->where("IDRestaurante", $request->rstId)
        //                             ->first();
        return $restaurante;
    }


}
