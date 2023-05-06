<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\MessageBag;

use App\Exports\PREsporteSingleTeamItemExport;
use App\Exports\PREsporteSingleTeamExport;
use App\Exports\PREsporteTeamItemExport;
use App\Exports\PREsporteTeamExport;

use App\Evento;
use App\Clube;
use Auth;
use Excel;

class ExportController extends Controller
{
    /*
     *
     * EXPORTADOR - PR ESPORTE LISTA DE JOGADORES - XADREZ INDIVIDUAL
     *
     */


    public function export_presporte_single($id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        return Excel::download(new PREsporteSingleTeamExport($evento->id), 'single.xlsx');
    }
    /*
     *
     * EXPORTADOR - PR ESPORTE LISTA DE JOGADORES - XADREZ POR EQUIPES
     *
     */


    public function export_presporte_team($id)
    {
        $evento = Evento::find($id);
        $user = Auth::user();
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/" . $evento->id);
        }

        $clube = Clube::find(1193);

        return Excel::download(new PREsporteTeamExport($evento->id), 'teams.xlsx');
    }
}
