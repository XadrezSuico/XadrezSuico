<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_titles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("players_id")->unsigned();
            $table->foreign("players_id")->references("id")->on("enxadrista");
            $table->bigInteger("titles_id")->unsigned();
            $table->foreign("titles_id")->references("id")->on("titles");
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
        Schema::dropIfExists('player_titles');
    }
}
