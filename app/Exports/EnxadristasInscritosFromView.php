<?php

namespace App\Exports;

use App\Enxadrista;
use App\Evento;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EnxadristasInscritosFromView implements FromView
{

    /**
     * @return \Illuminate\Support\Collection
     */
    private $evento;

    public function setEvento($id)
    {
        $evento = Evento::find($id);
        $this->evento = $evento;
    }

    public function view(): View
    {
        // return Enxadrista::all();
        if ($this->evento) {
            $evento = $this->evento;
            return view('exports.sm.inscritos', compact('evento'));
        }
        return false;
    }
}
