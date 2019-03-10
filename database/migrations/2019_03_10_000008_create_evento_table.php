<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'evento';

    /**
     * Run the migrations.
     * @table evento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 100);
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->string('local', 100);
            $table->unsignedInteger('grupo_evento_id');
            $table->unsignedInteger('cidade_id');

            $table->index(["cidade_id"], 'fk_evento_cidade1_idx');

            $table->index(["grupo_evento_id"], 'fk_evento_grupo_evento1_idx');
            $table->timestamps();


            $table->foreign('cidade_id', 'fk_evento_cidade1_idx')
                ->references('id')->on('cidade')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('grupo_evento_id', 'fk_evento_grupo_evento1_idx')
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
