<?php

use App\Title;
use Illuminate\Database\Migrations\Migration;

class UpdateCBXTitlesAbbrNmcWnmcWnm extends Migration
{
    /**
     * Converte abreviações CBX para nomenclatura FIDE/Swiss-Manager,
     * preservando ids e vínculos em player_titles.
     *
     * @return void
     */
    public function up()
    {
        $conversions = [
            'CMN' => 'NMC',
            'CMF' => 'WNMC',
            'MNF' => 'WNM',
        ];

        foreach ($conversions as $oldAbbr => $newAbbr) {
            Title::where('entities_id', 2)
                ->where('abbr', $oldAbbr)
                ->update(['abbr' => $newAbbr]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $conversions = [
            'NMC' => 'CMN',
            'WNMC' => 'CMF',
            'WNM' => 'MNF',
        ];

        foreach ($conversions as $oldAbbr => $newAbbr) {
            Title::where('entities_id', 2)
                ->where('abbr', $oldAbbr)
                ->update(['abbr' => $newAbbr]);
        }
    }
}
