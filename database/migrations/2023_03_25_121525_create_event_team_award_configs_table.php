<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTeamAwardConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_team_award_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('event_team_awards_id')->unsigned();
            $table->foreign('event_team_awards_id')->references('id')->on("event_team_awards");
            $table->string('key');

            $table->enum('value_type',["integer","float","decimal","boolean","string"]);

            $table->integer("integer")->nullable();
            $table->float("float")->nullable();
            $table->decimal("decimal",9,2)->nullable();
            $table->boolean("boolean")->nullable();
            $table->string("string")->nullable();
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
        Schema::dropIfExists('event_team_award_configs');
    }
}
