<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaTorneioTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'categoria_torneio';

    /**
     * Run the migrations.
     * @table categoria_torneio
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('categoria_id');
            $table->unsignedInteger('torneio_id');

            $table->index(["torneio_id"], 'fk_categoria_torneio_torneio1_idx');

            $table->index(["categoria_id"], 'fk_categoria_torneio_categoria1_idx');
            $table->timestamps();


            $table->foreign('torneio_id', 'fk_categoria_torneio_torneio1_idx')
                ->references('id')->on('torneio')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('categoria_id', 'fk_categoria_torneio_categoria1_idx')
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
