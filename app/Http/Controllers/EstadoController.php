<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Estado;

class EstadoController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }

    
    public function buscaEstado($pais_id)
    {
        $estados = Estado::where([
            ["pais_id", "=", $pais_id],
        ])->get();
        $results = array();
        foreach ($estados as $estado) {
            $results[] = array("id" => $estado->id, "text" => $estado->nome);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }
}
