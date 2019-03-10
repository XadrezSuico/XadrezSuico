<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTorneioTemplateGrupoEventoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'torneio_template_grupo_evento';

    /**
     * Run the migrations.
     * @table torneio_template_grupo_evento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('grupo_evento_id');
            $table->unsignedInteger('torneio_template_id');

            $table->index(["torneio_template_id"], 'fk_torneio_template_grupo_evento_torneio_template1_idx');

            $table->index(["grupo_evento_id"], 'fk_torneio_template_grupo_evento_grupo_evento1_idx');


            $table->foreign('grupo_evento_id', 'fk_torneio_template_grupo_evento_grupo_evento1_idx')
                ->references('id')->on('grupo_evento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('torneio_template_id', 'fk_torneio_template_grupo_evento_torneio_template1_idx')
                ->references('id')->on('torneio_template')
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
