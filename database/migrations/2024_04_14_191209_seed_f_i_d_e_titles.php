<?php

use App\Title;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedFIDETitles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $titles = [
            [1, "CM", "Candidato a Mestre",false,false],
            [1, "FM","Mestre FIDE", false, false],
            [1, "IM","Mestre Internacional", false, false],
            [1, "GM","Grande Mestre", false, false],
            [1, "WCM","Candidato a Mestre Feminino", true, false],
            [1, "WFM","Mestre FIDE Feminino", true, false],
            [1, "WIM","Mestre Internacional Feminino", true, false],
            [1, "WGM","Grande Mestre Feminino", true, false],
        ];

        foreach ($titles as $tit) {
            if (Title::where([["entities_id", "=", $tit[0]],["abbr","=",$tit[1]]])->count() > 0) {
                $title = Title::where([["entities_id", "=", $tit[0]], ["abbr", "=", $tit[1]]])->first();
            } else {
                $title = new Title;
                $title->entities_id = $tit[0];
                $title->abbr = $tit[1];
            }

            $title->name = $tit[2];
            $title->is_for_women = $tit[3];
            $title->is_online = $tit[4];
            $title->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
