<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRatingTableAddEntidade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->tinyinteger("entidade")->nullable()->after("valor")->comment = "0 - FIDE; 1 - CBX; 2 - LBX";
            $table->tinyinteger("tipo_modalidade")->nullable()->after("valor")->comment = "0 - STD; 1 - RPD; 2 - BTZ";
            $table->bigInteger('tipo_ratings_id')->unsigned()->nullable()->change();
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
