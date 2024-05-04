<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Evento;
use App\Clube;

class PREsporteTeamExport implements WithMultipleSheets
{
    use Exportable;

    private $event;
    private $fill_blanks;
    public function __construct($event_id, $fill_blanks = false){
        $this->event = Evento::find($event_id);
        $this->fill_blanks = $fill_blanks;
    }


    public function sheets(): array
    {
        $sheets = [];

        foreach($this->event->clubesInscritos() as $clube) {
            $sheets[] = new PREsporteTeamItemExport($this->event->id, $clube->id, $this->fill_blanks);
        }

        return $sheets;
    }
}
