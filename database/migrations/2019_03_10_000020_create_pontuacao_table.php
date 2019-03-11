<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePontuacaoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'pontuacao';

    /**
     * Run the migrations.
     * @table pontuacao
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('posicao');
            $table->decimal('pontuacao', 3, 2);
            $table->unsignedInteger('torneio_id')->nullable();
            $table->unsignedInteger('evento_id')->nullable();
            $table->unsignedInteger('grupo_evento_id')->nullable();

            $table->index(["grupo_evento_id"], 'fk_pontuacao_grupo_evento1_idx');

            $table->index(["torneio_id"], 'fk_pontuacao_torneio1_idx');

            $table->index(["evento_id"], 'fk_pontuacao_evento1_idx');


            $table->foreign('torneio_id', 'fk_pontuacao_torneio1_idx')
                ->references('id')->on('torneio')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('evento_id', 'fk_pontuacao_evento1_idx')
                ->references('id')->on('evento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('grupo_evento_id', 'fk_pontuacao_grupo_evento1_idx')
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
