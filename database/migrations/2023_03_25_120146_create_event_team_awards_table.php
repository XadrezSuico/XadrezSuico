<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTeamAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_team_awards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('events_id')->unsigned()->nullable();
            $table->foreign('events_id')->references("id")->on("evento");
            $table->integer('event_groups_id')->unsigned()->nullable();
            $table->foreign('event_groups_id')->references("id")->on("grupo_evento");
            $table->string('name');
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
        Schema::dropIfExists('event_team_awards');
    }
}
