<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventTeamAwardTableAddCalculatePublic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("event_team_awards", function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->after("name")->comment("Que está público");
            $table->boolean('is_can_calculate')->default(true)->after("name")->comment("Que permite calcular");
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
