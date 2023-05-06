<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use App\Evento;
use App\Clube;

class PREsporteSingleTeamItemExport implements FromView, WithStyles
{
    private $event;
    private $club;
    public function __construct($event_id,$club_id){
        $this->event = Evento::find($event_id);
        $this->club = Clube::find($club_id);
    }

    public function view(): View
    {
        return view('exports.presporte.single', [
            'evento' => $this->event,
            'clube' => $this->club
        ]);
    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 16]],
            2    => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            'C'  => ['font' => ['size' => 16]],
        ];
    }
}
