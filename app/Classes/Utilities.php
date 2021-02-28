<?php
namespace App\Classes;

use stdClass;
use App\Models\FacturaPayloadCabecera;
use App\Models\FacturaPayloadDetalle;
use App\Models\FacturaPayloadFormasPago;
use App\Models\FacturaPayloadModificador;
use Validator;

class Utilities
{
    protected function validate_data($to_validate, $rules) {
        $validator = Validator::make(json_decode(json_encode($to_validate),true), $rules);
        $toReturn = new stdClass();
        $toReturn->pass = true;
        $toReturn->message = 'Validado';
        $toReturn->data = $to_validate;
        if ($validator->fails()) {
            $toReturn->pass = false;
            $toReturn->message = 'Error al validar';
        }
        return $toReturn;
    }

    public function check_if_cabecera($to_verify) {
        $toCheckBase = new FacturaPayloadCabecera();
        return $this->validate_data($to_verify, $toCheckBase->get_rules());
    }

    public function check_if_detalle($to_verify) {
        $toCheckBase = new FacturaPayloadDetalle();
        return $this->validate_data($to_verify, $toCheckBase->get_rules());
    }

    public function check_if_modificador($to_verify) {
        $toCheckBase = new FacturaPayloadModificador();
        return $this->validate_data($to_verify, $toCheckBase->get_rules());
    }

    public function check_if_formas_pago($to_verify) {
        $toCheckBase = new FacturaPayloadFormasPago();
        return $this->validate_data($to_verify, $toCheckBase->get_rules());
    }

    public function httpGet($url, $data=NULL) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $headersSend = array('Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headersSend);
        $response = curl_exec($ch);
        if (curl_error($ch)) {
            trigger_error('Curl Error:' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }

    public function httpPost($url, $data=NULL) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $headersSend = array('Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headersSend);
        $response = curl_exec($ch);
        if (curl_error($ch)) {
            trigger_error('Curl Error:' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }
}
