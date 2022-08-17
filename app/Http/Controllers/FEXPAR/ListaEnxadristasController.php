<?php

namespace App\Http\Controllers\FEXPAR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Enxadrista;

class ListaEnxadristasController extends Controller
{
    public function todos(){
        if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
            $enxadristas = Enxadrista::all();

            return view("paginas_especiais.fexpar.todos_enxadristas",compact("enxadristas"));
        }
        return abort(404);
    }
}
