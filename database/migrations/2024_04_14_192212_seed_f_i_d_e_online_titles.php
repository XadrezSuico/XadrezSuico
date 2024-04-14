<?php

use App\Title;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedFIDEOnlineTitles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $titles = [
            [1, "ACM","Candidato a Mestre de Arena (Online)", false, true],
            [1, "AFM","Mestre FIDE de Arena (Online)", false, true],
            [1, "AIM","Mestre Internacional de Arena (Online)", false, true],
            [1, "AGM","Grande Mestre de Arena (Online)", false, true],
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
            $title->is_used = true;
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
