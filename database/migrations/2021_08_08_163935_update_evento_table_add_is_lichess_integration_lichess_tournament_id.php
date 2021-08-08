<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEventoTableAddIsLichessIntegrationLichessTournamentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evento', function (Blueprint $table) {
            $table->string('lichess_tournament_id')->nullable()->after("is_lichess");
            $table->string('lichess_team_id')->nullable()->after("is_lichess");
            $table->boolean('is_lichess_integration')->default(false)->after("is_lichess");
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
