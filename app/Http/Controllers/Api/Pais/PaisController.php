<?php

namespace App\Http\Controllers\Api\Pais;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{

    public function index(){
        $pais = Pais::all();
        return  $pais;
    }

    

}
