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
    public function __construct($event_id){
        $this->event = Evento::find($event_id);
    }


    public function sheets(): array
    {
        $sheets = [];

        foreach($this->event->clubesInscritos() as $clube) {
            $sheets[] = new PREsporteTeamItemExport($this->event->id, $clube->id);
        }

        return $sheets;
    }
}
