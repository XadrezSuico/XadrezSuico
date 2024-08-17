<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInscricaoTableAddIsDraw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("inscricao", function (Blueprint $table) {
            $table->boolean('is_draw')->default(false)->after("is_aceito_imagem")->comment("Que foi identificado no processo que este enxadrista está empatado com o outro.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
