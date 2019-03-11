<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaTorneioTemplateTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'categoria_torneio_template';

    /**
     * Run the migrations.
     * @table categoria_torneio_template
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('torneio_template_id');
            $table->unsignedInteger('categoria_id');
            $table->timestamps();

            $table->index(["torneio_template_id"], 'fk_categoria_torneio_template_torneio_template1_idx');

            $table->index(["categoria_id"], 'fk_categoria_torneio_template_categoria1_idx');


            $table->foreign('torneio_template_id', 'fk_categoria_torneio_template_torneio_template1_idx')
                ->references('id')->on('torneio_template')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('categoria_id', 'fk_categoria_torneio_template_categoria1_idx')
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
