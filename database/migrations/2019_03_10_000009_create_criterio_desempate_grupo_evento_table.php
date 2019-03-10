<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriterioDesempateGrupoEventoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'criterio_desempate_grupo_evento';

    /**
     * Run the migrations.
     * @table criterio_desempate_grupo_evento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('grupo_evento_id');
            $table->unsignedInteger('criterio_desempate_idcriterio_desempate');
            $table->unsignedInteger('tipo_torneio_id');
            $table->integer('prioridade');

            $table->index(["tipo_torneio_id"], 'fk_criterio_desempate_grupo_evento_tipo_torneio1_idx');

            $table->index(["criterio_desempate_idcriterio_desempate"], 'fk_criterio_desempate_grupo_evento_criterio_desempate1_idx');

            $table->index(["grupo_evento_id"], 'fk_criterio_desempate_grupo_evento_grupo_evento1_idx');
            $table->timestamps();


            $table->foreign('grupo_evento_id', 'fk_criterio_desempate_grupo_evento_grupo_evento1_idx')
                ->references('id')->on('grupo_evento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('criterio_desempate_idcriterio_desempate', 'fk_criterio_desempate_grupo_evento_criterio_desempate1_idx')
                ->references('idcriterio_desempate')->on('criterio_desempate')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tipo_torneio_id', 'fk_criterio_desempate_grupo_evento_tipo_torneio1_idx')
                ->references('id')->on('tipo_torneio')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
