<?php

namespace App\Http\Controllers\Api\FacturaPayload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacturaPayloadController extends Controller
{
    public function get(Request $request, $pais) {
        return $pais . ' get ';
    }

    public function post(Request $request, $pais) {
        return $pais . ' post ';
    }

    public function put(Request $request, $pais) {
        return $pais . ' put ';
    }

    public function delete(Request $request, $pais) {
        return $pais . ' dalete ';
    }
}
