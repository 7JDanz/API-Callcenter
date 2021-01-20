<?php

namespace App\Http\Controllers;

use App\Models\Geolocalizacion;
use App\Models\HorarioAtencionRestaurante;
use App\Models\Locales;
use App\Models\Restaurante;
use Illuminate\Http\Request;


use Location\Coordinate;
use Location\Polygon;



class GeolocalizacionController extends Controller
{
   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function obtenerREstaurante(Request $request)
    {
        return    Restaurante::where("IDRestaurante", $request->rstId)
        ->first();;
    }

    public function getDatosRestaurante(Request $request)
    {

        $restaurante = Restaurante::where("IDRestaurante", $request->rstId)
        ->first();
        // $restaurante = Restaurante::with(['geolocalizacion'])
        //                             ->where("IDRestaurante", $request->rstId)
        //                             ->first();
        return $restaurante;
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
                    $request->lng,
                    $request->lat,
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

                return ["direccion"  =>  $formattedAddress  ,"latitude" => $latitude ,  "longitude" => $longitude];
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



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCercania(Request $request)
    {

        $polygonResults = [];
        $pointFind = new Coordinate($request->lat,  $request->lng);
        $polygonos = Geolocalizacion::all();
        $encontro  = false;
        foreach ($polygonos as $poligon) {
            $geofence = new Polygon();

            foreach ($poligon->coordinates as $coodinate) {
                $geofence->addPoint(new Coordinate($coodinate["lat"], $coodinate["lng"]));
            }

            $existe = $geofence->contains($pointFind);

            if ($existe) {
                $encontro = true;
                $polygonResults = $poligon;
                break;
            }
        }

        if ($encontro){
            $restaurante = Restaurante::with(['geolocalizacion'])
            ->where("IDRestaurante", $polygonResults->id_restaurante)
            ->first();
        }else {
            $restaurante  = ["estado" => "201" ,  "mensaje" => "No encontrado"];
        }


        return $restaurante;
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Geolocalizacion::all();
    }

    public function todos()
    {
        return Geolocalizacion::all();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $geolocalizacion = Geolocalizacion::create($request->all());
        return $geolocalizacion;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($pais,$id)
    {
        return Geolocalizacion::find($id);
    }


    public function mostrarGEo($id)
    {

        return Geolocalizacion::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return   Geolocalizacion::find($id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Geolocalizacion::findOrFail($id)->delete();
    }
}
