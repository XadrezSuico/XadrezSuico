<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriterioDesempateGrupoEventoGeralTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'criterio_desempate_grupo_evento_geral';

    /**
     * Run the migrations.
     * @table criterio_desempate_grupo_evento_geral
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('prioridade');
            $table->unsignedInteger('criterio_desempate_id');
            $table->unsignedInteger('grupo_evento_id');

            $table->index(["criterio_desempate_id"], 'fk_criterio_desempate_grupo_evento_geral_criterio_desempate_idx');

            $table->index(["grupo_evento_id"], 'fk_criterio_desempate_grupo_evento_geral_grupo_evento1_idx');
            $table->timestamps();


            $table->foreign('criterio_desempate_id', 'fk_criterio_desempate_grupo_evento_geral_criterio_desempate_idx')
                ->references('id')->on('criterio_desempate')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('grupo_evento_id', 'fk_criterio_desempate_grupo_evento_geral_grupo_evento1_idx')
                ->references('id')->on('grupo_evento')
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
