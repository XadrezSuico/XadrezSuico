<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventClassificateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_classificate_rules', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('event_classificates_id')->unsigned();
            $table->foreign('event_classificates_id')->references("id")->on("event_classificates");

            $table->enum('type',["position", "position-absolute", "pre-classificate","place-by-quantity"]);
            $table->integer('value')->default(0);

            $table->integer('event_id')->unsigned()->nullable();
            $table->foreign('event_id')->references("id")->on("evento");

            $table->boolean('is_absolute')->default(0)->comment("Se vale exclusivamente para aquela posição da regra ou se repassa para o próximo caso esteja impedido de uso da vaga.");

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
        Schema::dropIfExists('event_classificate_rules');
    }
}
