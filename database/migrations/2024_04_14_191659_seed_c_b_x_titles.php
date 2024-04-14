<?php

use App\Title;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedCBXTitles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $titles = [
            [2, "CMN","Candidato a Mestre Nacional", false, false, false],
            [2, "NM","Mestre Nacional", false, false, true], // Nomenclatura FIDE
            [2, "CMF", "Candidato a Mestre Nacional Feminino", true, false, false],
            [2, "MNF", "Mestre Nacional Feminino", true, false, false],
            [2, "CMNO","Candidato a Mestre Nacional Online", false, true, false],
            [2, "MNO","Mestre Nacional Online", false, true, false],
        ];

        foreach ($titles as $tit) {
            if (Title::where([["entities_id", "=", $tit[0]], ["abbr", "=", $tit[1]]])->count() > 0) {
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
