<?php

namespace App\Exports;

use App\Enxadrista;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EnxadristasCompletoFromView implements FromView
{
    public function view(): View
    {

        $whereEnxadristas = Enxadrista::where([
            ["name","like","%.%"],
            ["name","like","%-%"],
            ["name","like","%_%"]
        ])->pluck('id')->toArray();

        $enxadristas = Enxadrista::whereNotIn("id",$whereEnxadristas)
        ->get();

        return view('exports.enxadristas',compact('enxadristas'));
    }
}
