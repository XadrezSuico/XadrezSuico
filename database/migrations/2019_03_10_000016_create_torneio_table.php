<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTorneioTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'torneio';

    /**
     * Run the migrations.
     * @table torneio
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 45);
            $table->unsignedInteger('evento_id');
            $table->unsignedInteger('tipo_torneio_id');
            $table->unsignedInteger('categoria_torneio_template_id')->nullable();

            $table->index(["tipo_torneio_id"], 'fk_torneio_tipo_torneio1_idx');

            $table->index(["categoria_torneio_template_id"], 'fk_torneio_categoria_torneio_template1_idx');

            $table->index(["evento_id"], 'fk_torneio_evento1_idx');
            $table->timestamps();


            $table->foreign('evento_id', 'fk_torneio_evento1_idx')
                ->references('id')->on('evento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tipo_torneio_id', 'fk_torneio_tipo_torneio1_idx')
                ->references('id')->on('tipo_torneio')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('categoria_torneio_template_id', 'fk_torneio_categoria_torneio_template1_idx')
                ->references('id')->on('categoria_torneio_template')
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
