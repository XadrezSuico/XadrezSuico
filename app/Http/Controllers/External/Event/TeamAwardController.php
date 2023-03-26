<?php

namespace App\Http\Controllers\External\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;

class TeamAwardController extends Controller
{
    public function standings($events_id)
    {
        $event = Evento::find($events_id);
        return view("evento.publico.team_award.standings", compact("event"));
    }
    public function list($events_id, $team_awards_id)
    {
        $event = Evento::find($events_id);
        $team_award = $event->event_team_awards()->where([["id","=",$team_awards_id]])->first();
        return view("evento.publico.team_award.list", compact("event", "team_award"));
    }
    public function verPontuacaoEnxadrista($grupo_evento_id, $enxadrista_id)
    {
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        $enxadrista = Enxadrista::find($enxadrista_id);
        if ($grupo_evento && $enxadrista) {
            return view("grupoevento.publico.enxadrista", compact("grupo_evento", "enxadrista"));
        }
        return redirect()->back();
    }
}
