<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventoAnuidadeCbxComprovantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evento_anuidade_cbx_comprovantes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('evento_id')->unsigned();
            $table->foreign('evento_id')->references('id')->on('evento');
            $table->integer('enxadrista_id')->unsigned();
            $table->foreign('enxadrista_id')->references('id')->on('enxadrista');
            $table->boolean('comprovante_recebido')->default(false);
            $table->timestamps();

            $table->unique(['evento_id', 'enxadrista_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evento_anuidade_cbx_comprovantes');
    }
}
