<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampoPersonalizadoEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campo_personalizado_eventos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('evento_id')->unsigned();
            $table->foreign('evento_id')->references("id")->on("evento");
            $table->bigInteger('campo_personalizados_id')->unsigned();
            $table->foreign('campo_personalizados_id')->references("id")->on("campo_personalizados");
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
        Schema::dropIfExists('campo_personalizado_eventos');
    }
}
