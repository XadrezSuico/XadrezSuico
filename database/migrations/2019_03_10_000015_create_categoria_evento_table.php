<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaEventoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'categoria_evento';

    /**
     * Run the migrations.
     * @table categoria_evento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('evento_id');
            $table->unsignedInteger('categoria_id');

            $table->index(["evento_id"], 'fk_categoria_evento_evento1_idx');

            $table->index(["categoria_id"], 'fk_categoria_evento_categoria1_idx');
            $table->timestamps();


            $table->foreign('evento_id', 'fk_categoria_evento_evento1_idx')
                ->references('id')->on('evento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('categoria_id', 'fk_categoria_evento_categoria1_idx')
                ->references('id')->on('categoria')
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
