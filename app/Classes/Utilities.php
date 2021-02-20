<?php
namespace App\Classes;

use stdClass;

class Utilities
{
    public function check_if_instanceOf($destinationClass, $sourceObject)
    {
        $destinationClassProperties = $this->get_keys($this->convert_to_array($destinationClass));
        $sourceObjectProperties = $this->get_keys($this->convert_to_array($sourceObject));
        $not_found = [];
        foreach($destinationClassProperties as $destinationClassProperty) {
            $existe = false;
            foreach($sourceObjectProperties as $sourceObjectProperty) {
                if ($sourceObjectProperty == $destinationClassProperty) {
                    $existe = true;
                }
            }
            if (!$existe) {
                array_push($not_found, $destinationClassProperty);
            }
        }
        $toReturn = new stdClass();
        $toReturn->pass = $not_found == [] ? true : false;
        $toReturn->message = $not_found == [] ? 'ok' : 'Falta: ' . join(', ', $not_found);
        return $toReturn;
    }

    private function convert_to_array($object) {
        return json_decode(json_encode($object), true);
    }

    private function get_keys($object_as_array) {
        $keys = [];
        foreach($object_as_array as $key=>$value) {
            array_push($keys, $key);
        }
        return $keys;
    }
}
