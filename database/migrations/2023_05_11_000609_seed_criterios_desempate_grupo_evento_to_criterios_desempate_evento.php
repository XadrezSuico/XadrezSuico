<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Evento;
use App\CriterioDesempateEvento;

class SeedCriteriosDesempateGrupoEventoToCriteriosDesempateEvento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach(Evento::all() as $evento){
            if(
                $evento->grupo_evento->criterios()->count() > 0 &&
                $evento->criterios()->count() == 0
            ){
                // IMPORTAÇÃO DOS CRITÉRIOS DE DESEMPATE
                foreach ($evento->grupo_evento->criterios->all() as $criterio_desempate_grupo_evento) {
                    $criterio_desempate_evento = new CriterioDesempateEvento;
                    $criterio_desempate_evento->evento_id = $evento->id;
                    $criterio_desempate_evento->softwares_id = $criterio_desempate_grupo_evento->softwares_id;
                    $criterio_desempate_evento->tipo_torneio_id = $criterio_desempate_grupo_evento->tipo_torneio_id;
                    $criterio_desempate_evento->prioridade = $criterio_desempate_grupo_evento->prioridade;
                    $criterio_desempate_evento->criterio_desempate_id = $criterio_desempate_grupo_evento->criterio_desempate_id;
                    $criterio_desempate_evento->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('criterios_desempate_evento', function (Blueprint $table) {
            //
        });
    }
}
