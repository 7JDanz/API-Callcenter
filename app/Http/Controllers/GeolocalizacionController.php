<?php

namespace App\Http\Controllers;

use App\Models\Geolocalizacion;
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
    public function getDatosRestaurante(Request $request)
    {

        $restaurante = Restaurante::with(['geolocalizacion'])
                                    ->where("IDRestaurante", $request->rstId)
                                    ->first();
        return $restaurante;
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
    public function show($id)
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
