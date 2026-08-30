<?php

use App\Title;
use Illuminate\Database\Migrations\Migration;

class UpdateCBXTitlesNmcWnmcWnm extends Migration
{
    /**
     * Converte abreviações e nomes dos títulos CBX para nomenclatura
     * FIDE/Swiss-Manager, preservando ids e vínculos em player_titles.
     *
     * @return void
     */
    public function up()
    {
        $conversions = [
            'CMN' => [
                'abbr' => 'NMC',
                'name' => 'Candidato a Mestre Nacional',
            ],
            'CMF' => [
                'abbr' => 'WNMC',
                'name' => 'Candidata a Mestre Nacional Feminino',
            ],
            'MNF' => [
                'abbr' => 'WNM',
                'name' => 'Mestre Nacional Feminino',
            ],
        ];

        foreach ($conversions as $oldAbbr => $data) {
            Title::where('entities_id', 2)
                ->where('abbr', $oldAbbr)
                ->update([
                    'abbr' => $data['abbr'],
                    'name' => $data['name'],
                ]);
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
            'NMC' => [
                'abbr' => 'CMN',
                'name' => 'Candidato a Mestre Nacional',
            ],
            'WNMC' => [
                'abbr' => 'CMF',
                'name' => 'Candidato a Mestre Nacional Feminino',
            ],
            'WNM' => [
                'abbr' => 'MNF',
                'name' => 'Mestre Nacional Feminino',
            ],
        ];

        foreach ($conversions as $oldAbbr => $data) {
            Title::where('entities_id', 2)
                ->where('abbr', $oldAbbr)
                ->update([
                    'abbr' => $data['abbr'],
                    'name' => $data['name'],
                ]);
        }
    }
}
