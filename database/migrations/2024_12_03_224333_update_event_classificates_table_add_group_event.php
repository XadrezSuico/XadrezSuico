<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventClassificatesTableAddGroupEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("event_classificates", function (Blueprint $table) {
            $table->integer('event_classificator_id')->unsigned()->nullable()->comment("Caso o classificador seja um evento.")->change();
            $table->integer('event_group_classificator_id')->after("event_classificator_id")->unsigned()->nullable()->comment("Caso o classificador seja a classificação do circuito.");
            $table->foreign('event_group_classificator_id')->references("id")->on("grupo_evento");
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
