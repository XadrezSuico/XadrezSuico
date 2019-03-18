<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;

class EventoController extends Controller
{
	public function __construct(){
		return $this->middleware("auth");
	}
	
	public function index(){
		$eventos = Evento::all();
		return view("evento.index",compact("eventos"));
	}
}
