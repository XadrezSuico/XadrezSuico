<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;

class TorneioController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index($id){
        $evento = Evento::find($id);
        $torneios = $evento->torneios->all();
		return view("evento.torneio.index",compact("evento","torneios"));
	}
}
