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
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /*
     *
     * EXPORTADOR - PR ESPORTE LISTA DE JOGADORES - XADREZ INDIVIDUAL
     *
     */


    public function export_presporte_single($id, Request $request)
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

        $fill_blanks = false;
        if($request->has("fill_blanks")){
            $fill_blanks = true;
        }

        return Excel::download(new PREsporteSingleTeamExport($evento->id, $fill_blanks), 'single.xlsx');
    }
    /*
     *
     * EXPORTADOR - PR ESPORTE LISTA DE JOGADORES - XADREZ POR EQUIPES
     *
     */


    public function export_presporte_team($id, Request $request)
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

        $fill_blanks = false;
        if ($request->has("fill_blanks")) {
            $fill_blanks = true;
        }

        return Excel::download(new PREsporteTeamExport($evento->id, $fill_blanks), 'teams.xlsx');
    }


    /*
     *
     * EXPORTADOR - PR ESPORTE LISTA DE JOGADORES - XADREZ INDIVIDUAL
     *
     */


    public function export_presporte_single_pdf($id, Request $request)
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

        $fill_blanks = false;
        if ($request->has("fill_blanks")) {
            $fill_blanks = true;
        }

        return Excel::download(new PREsporteSingleTeamExport($evento->id, $fill_blanks),'single.xlsx', \Maatwebsite\Excel\Excel::MPDF);
    }
    /*
     *
     * EXPORTADOR - PR ESPORTE LISTA DE JOGADORES - XADREZ POR EQUIPES
     *
     */


    public function export_presporte_team_pdf($id, Request $request)
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

        $fill_blanks = false;
        if ($request->has("fill_blanks")) {
            $fill_blanks = true;
        }

        return Excel::download(new PREsporteTeamExport($evento->id, $fill_blanks),'teams.xlsx', \Maatwebsite\Excel\Excel::MPDF);
    }
}
