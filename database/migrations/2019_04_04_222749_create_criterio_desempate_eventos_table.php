<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriterioDesempateEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criterio_desempate_eventos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('evento_id');
            $table->unsignedInteger('criterio_desempate_id');
            $table->unsignedInteger('tipo_torneio_id');
            $table->bigInteger('softwares_id')->unsigned()->nullable();
            


            $table->foreign('evento_id')
                ->references('id')->on('evento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('criterio_desempate_id')
                ->references('id')->on('criterio_desempate')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tipo_torneio_id')
                ->references('id')->on('tipo_torneio')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign("softwares_id")->references("id")->on("softwares");            
            $table->integer('prioridade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('criterio_desempate_eventos');
    }
}
