<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatEventTeamScoreAddRegistrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("event_team_scores", function (Blueprint $table) {
            $table->json('registrations_id')->nullable()->after("score")->comment("Indica quais inscrições foram consideradas para o cálculo.");
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
