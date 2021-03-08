<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geolocalizacion;
use App\Models\HorarioAtencionRestaurante;
use App\Models\Locales;
use App\Models\Restaurante;
use Illuminate\Support\Arr;
use Location\Coordinate;
use Location\Polygon;
use Illuminate\Support\Facades\Validator;

class GeolocalizacionController extends Controller
{

    public function invertirCoordenadas($coordenadas)
    {
        $cordenadas = collect(
            $coordenadas
        );
        //Este map invierte los datos enviados colocando primero longitud seguido de latitud.
        $coordenadasFormateadasLngLat = $cordenadas->map(function ($item, $key) {
            return  [$item[1],  $item[0]];
        });
        return   $coordenadasFormateadasLngLat;
    }

    public function index()
    {
        return Locales::firstOrFail()->raw(function ($collection) {
            return  $collection->aggregate([
                ['$project' => [
                    "_id" => 0,
                    "IDRestaurante" =>  '$IDRestaurante',
                    "codZipCode" =>  '$codZipCode',
                    "poligonoCobertura.coordenadas" =>  '$poligonoCobertura.coordinates'
                ]]
            ]);
        });
    }

    public function store(Request $request)
    {

        $rules = [
            'IDRestaurante' => 'integer|required',
            'codZipCode' => 'required',
            'poligonoCobertura' => 'required',
            'invertir' => 'required',
            'poligonoCobertura.coordenadas' => 'required|array|min:1|max:1', //
        ];
        $messages = [
            'invertir.required' => 'El campo invertir es obligatorio',
            'codZipCode.required' => 'El campo codZipCode es obligatorio',
            'IDRestaurante.required' => 'El campo IDRestaurante es obligatorio',
            'IDRestaurante.integer' => 'EL campo IDRestaurante debe ser un número',
            'poligonoCobertura.required' => 'Falta el objeto [poligonoCobertura]',
            'poligonoCobertura.coordenadas.required' => 'Falta el objeto [poligonoCobertura.coordenadas]',
            'poligonoCobertura.coordenadas.max' => 'El array [poligonoCobertura.coordenadas] en su raíz debe tener máximo 1 solo elemento',
            'poligonoCobertura.coordenadas.min' => 'El array [poligonoCobertura.coordenadas] en su raíz debe tener mínimo 1 solo elemento',
        ];
        $validator = Validator::make($request->all(),  $rules, $messages);

        if (!$validator->passes())
            return  response(["estado" => 400, "mensaje" =>   $validator->errors()->all()], 400);


        $cantidadCoordenadas = count($request->poligonoCobertura["coordenadas"][0]);

        if ($cantidadCoordenadas < 4) {
            return  response(["estado" => 400, "mensaje" =>    "Dentro del array poligonoCobertura.coordenadas[0].* deben existir mínimo 4 puntos de geolocalización. [longitud,latitud] "], 400);
        }


        $coordenadasFormateadasLngLat = $request->poligonoCobertura["coordenadas"][0];



        if ($request->invertir == 1) {
            $coordenadasFormateadasLngLat = $this->invertirCoordenadas($request->poligonoCobertura["coordenadas"][0]);
            $coordenadasFormateadasLngLat =     $coordenadasFormateadasLngLat->toarray();
        }



        $coordenadas = null;

        if ($coordenadasFormateadasLngLat[0]    !=  $coordenadasFormateadasLngLat[$cantidadCoordenadas - 1]) {
            $coordenadas = $coordenadasFormateadasLngLat;

            array_push($coordenadas, $coordenadasFormateadasLngLat[0]);
        }

        $local = [
            "IDRestaurante" =>  $request->IDRestaurante,
            "codZipCode" => $request->codZipCode,
            "poligonoCobertura" => [
                "coordinates" =>  [
                    $coordenadas
                ],
                "type" => "Polygon"
            ],
        ];

        return  Locales::create($local);
    }

    public function show($pais, $id)
    {
        return  Locales::find($id);

        // return  Locales::firstOrFail() ->raw(function ($collection) {
        //             return  $collection->aggregate([
        //                 ['$project' => [
        //                     "_id" => 0,
        //                     "IDRestaurante" =>  '$IDRestaurante',
        //                     "codZipCode" =>  '$codZipCode',
        //                     "poligonoCobertura.coordenadas" =>  '$poligonoCobertura.coordinates'
        //                 ]]
        //             ]);
        //         })->where ('_id' , '=', $id);
    }

    public function update(Request $request, $id)
    {
        return  ["Actualziar" => $request->all()]; //  Geolocalizacion::find($id)->update($request->all());
    }

    public function destroy($id)
    {
        return  ["error" => "No se permite eliminar."];
        //  Locales::findOrFail($id)->delete();
    }

    public function pruebasError(  Request $request)
    {


        return Locales::all();
    }
    public function getRestaurantesCercanos($pais, Request $request)
    {
        $rules = [
            'latitud' => 'required',
            'longitud' => 'required'
        ];
        $messages = [
            'latitud.required' => 'El campo latitud es obligatorio',
            'longitud.required' => 'El campo longitud es obligatorio',
        ];
        $validator = Validator::make($request->all(),  $rules, $messages);

        if (!$validator->passes())
            return  response(["estado" => 400, "mensaje" =>   $validator->errors()->all()], 400);


        try {
            $results =  Locales::with(
                [
                    'restaurante' => function ($restaurante) {
                        $restaurante->select(
                            'IDRestaurante',
                            'IDTienda',
                            'Nombre',
                            'Direccion',
                            'Telefono',
                            'Latitud',
                            'Longitud'
                        )->with(array('horariosAtencion' => function ($horarios) {
                            $horarios->select(
                                'IDRestaurante',
                                'Dia',
                                'horaInicio',
                                'horaFin'
                            );
                        }));
                    },
                ]
            )->where('poligonoCobertura',  [
                '$geoIntersects' => [
                    '$geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            $request->longitud,
                            $request->latitud,
                        ],
                    ]
                ]
            ])->firstOrFail();
        } catch (\Exception $e) {
            return ["estado" => 400, "mensaje" => "Ningún resultado para las coordenadas [latitud: $request->latitud, longitud:$request->longitud ]", "error" => $e];
        }


        $polygonoRestarante  = null;
        try {
            $polygonoRestarante =  $results->firstOrFail()->raw(function ($collection) {
                return  $collection->aggregate([
                    ['$project' => [
                        "_id" => 0,
                        // "codigoTienda" =>  '$IDRestaurante',
                        "latitud" =>  '$latitud',
                        "longitud" =>  '$longitud',
                        "codZipCode" =>  '$codZipCode',
                        "coordenadas" =>  '$poligonoCobertura.coordinates'
                    ]]
                ]);
            });
        } catch (\Exception $e) {
            return ["estado" => 400, "mensaje" => "Ningún resultado para las coordenadas [latitud: $request->latitud, longitud:$request->longitud ]", "error" => $e];
        }

        $respuesta = $results->restaurante;
        $restaurante = Arr::add($respuesta, "poligonoCobertura", $polygonoRestarante[0]);

        return  ["restaurante" => $restaurante];
    }

    public function getRestaurantesCercanosOld(Request $request)
    {
        $c =  Locales::with(array('restaurante.horariosAtencion' => function ($query) {
            $query->select('IDRestaurante', 'Dia', 'horaInicio', 'horaFin');
        }));

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
        ]);
        return $c->get();
        //  return     $c->get()->makeHidden(['nombre','restaurante.horarios_atencion']) ;
    }


    public function obtenerPuntos(Request $request)
    {
        $rules = [
            'direccion' => 'required'
        ];
        $messages = [
            'direccion.required' => 'El campo direccion es obligatorio',
        ];
        $validator = Validator::make($request->all(),  $rules, $messages);

        if (!$validator->passes())
            return  response(["estado" => 400, "mensaje" =>   $validator->errors()->all()], 400);


        $address = urlencode($request->direccion);
        $googleMapUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyAgo_FfLGAjc0uszGFn2Za_Dssold8k_HM";
        $geocodeResponseData = file_get_contents($googleMapUrl);
        $responseData = json_decode($geocodeResponseData, true);

        if ($responseData['status'] == 'OK') {
            $latitude = isset($responseData['results'][0]['geometry']['location']['lat']) ? $responseData['results'][0]['geometry']['location']['lat'] : "";
            $longitude = isset($responseData['results'][0]['geometry']['location']['lng']) ? $responseData['results'][0]['geometry']['location']['lng'] : "";
            $formattedAddress = isset($responseData['results'][0]['formatted_address']) ? $responseData['results'][0]['formatted_address'] : "";

            if ($latitude && $longitude && $formattedAddress) {

                return ["direccion"  =>  $formattedAddress, "latitud" => $latitude,  "longitud" => $longitude];
            } else {
                return false;
            }
        } else {
            echo "ERROR: {$responseData['status']}";
            return false;
        }
    }



    public function getDatosRestaurante(Request $request)
    {
        $restaurante = Restaurante::where("IDRestaurante", $request->IDRestaurante)
            ->first();
        // $restaurante = Restaurante::with('localesMongo')
        //                             ->where("IDRestaurante", $request->IDRestaurante)
        //                             ->first();
        return $restaurante;
    }
}
