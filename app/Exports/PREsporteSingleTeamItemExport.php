<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use App\Evento;
use App\Clube;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Drawing;

class PREsporteSingleTeamItemExport implements FromView, WithStyles, WithColumnWidths, WithEvents
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
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                // $event->sheet->getDelegate()
                //     ->getStyle('A8')
                //     ->applyFromArray(['alignment' => ['wrapText' => true], 'size' => ['height' => 30]]);

                $cells_all = array("E3");
                $cells_bottom = array();
                $cells_top = array();

                $l = 4;
                foreach ($this->event->inscritosPorClube($this->club->id) as $id_categoria => $inscricoes){
                    for($a = "A"; $a < "L"; $a++){
                        $cells_bottom[] = "{$a}{$l}";
                    }
                    $l++;
                    for ($a = "A"; $a <= "L"; $a++) {
                        $cells_all[] = "{$a}{$l}";
                    }
                    // $cells_all[] = "A{$l}";
                    // $cells_all[] = "B{$l}";
                    // $cells_all[] = "C{$l}";
                    // $cells_all[] = "D{$l}";
                    // $cells_all[] = "E{$l}";
                    // $cells_all[] = "F{$l}";
                    // $cells_all[] = "G{$l}";
                    // $cells_all[] = "H{$l}";
                    // $cells_all[] = "I{$l}";
                    // $cells_all[] = "K{$l}";

                    foreach($inscricoes as $inscricao){
                        $l++;
                        for ($a = "A"; $a <= "L"; $a++) {
                            $cells_all[] = "{$a}{$l}";
                        }
                    }
                    $l++;
                }
                $l++;
                $l++;
                $l++;
                $cells_top[] = "A{$l}";


                foreach ($cells_all as $cell) {
                    $event->sheet->getStyle($cell)->applyFromArray([
                        'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                            'bottom' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                            'left' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                }
                foreach ($cells_bottom as $cell) {
                    $event->sheet->getStyle($cell)->applyFromArray([
                        'borders' => [
                            'bottom' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                }
                foreach ($cells_top as $cell) {
                    $event->sheet->getStyle($cell)->applyFromArray([
                        'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                }


                // for ($letter = "B"; $letter <= "K"; $letter++) {
                //     $event->sheet->getStyle($letter . (41 + $total_taxes + $total_benefits))->applyFromArray([
                //         'borders' => [
                //             'bottom' => [
                //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                //                 'color' => ['argb' => '000000'],
                //             ],
                //         ],
                //     ]);
                // }
            }
        ];
    }
    public function columnWidths(): array
    {
        $default_value = 7.5;
        $values = [];


        for ($letter = "A"; $letter <= "L"; $letter++) {
            $values[$letter] = $default_value;
        }
        return $values;
    }
}
