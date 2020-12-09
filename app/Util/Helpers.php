<?php


namespace App\Util;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Easy\Load;

class Helpers {
    static function leerParametrosEnv($nombresConfiguraciones) {
        $parametros = [];
        foreach ($nombresConfiguraciones as $nombre) {
            $valor = env($nombre);
            if (!$valor) {
                $mensajeError = "No se pudo leer el parametro de configuracion '$nombre' en el archivo .env";
                Log::error($mensajeError);
                return false;
            }
            $parametros[$nombre] = $valor;
        }
        return $parametros;
    }

    static function isJSON($string) {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function decodeJWT($jwt) {
        if ($jwt) {
            list($header, $claims, $signature) = explode('.', $jwt);

            $header = self::decodeFragment($header);
            $claims = self::decodeFragment($claims);
            $signature = (string) base64_decode($signature);

            return [
                'header' => $header,
                'claims' => $claims,
                'signature' => $signature
            ];
        }

        return false;
    }

    protected static function decodeFragment($value) {
        return (array) json_decode(base64_decode($value));
    }

    public static function revisarToken($token) {
        $jwk = JWKFactory::createFromKeyFile(
            storage_path("security/public.txt"), // The filename
            ''
        );

        try{
            $jwt = Load::jws($token) // We want to load and verify the token in the variable $token
            ->algs(['RS256', 'RS512']) // The algorithms allowed to be used
            ->exp() // We check the "exp" claim
            // ->iat(1000) // We check the "iat" claim. Leeway is 1000ms (1s)
            ->nbf() // We check the "nbf" claim
            // ->aud('audience1') // Allowed audience
            // ->iss('issuer') // Allowed issuer
            // ->sub('subject') // Allowed subject
            // ->jti('0123456789') // Token ID
            ->key($jwk) // Key used to verify the signature
            ->run();
        } catch( \Exception $ex) {
            return ["valido" => false, "token" => null, "error" => $ex->getMessage()];
        }

        return ["valido" => true, "token" => $jwt];
    }

    public static function selectJSON($strQuery, $params, $nombreConexion=null) {

        $pdo = DB::connection($nombreConexion)->getPdo();
        $pdo->setAttribute(\PDO::SQLSRV_ATTR_QUERY_TIMEOUT, 120);
        $stmt = $pdo->prepare($strQuery);
        $stmt->execute($params);
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $str = "";
        foreach ($res as $val) {
            $str .= reset($val);
        }
        return $str;
    }

    public static function buscarNombreConexionAzurePorPais($pais="ecu"){
        $pais=trim(strtolower($pais));
        return "sqlsrv_mxp_".$pais;
    }

    public static function buscarNombreConexionClientesPorPais($pais="ecu"){
        $pais=trim(strtolower($pais));
        return "sqlsrv_clientes_".$pais;
    }

}