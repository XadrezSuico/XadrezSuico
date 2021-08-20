<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCriterioDesempateTableAddIsLichess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("criterio_desempate", function (Blueprint $table) {
            $table->boolean('is_sm')->default(true);
            $table->boolean('is_lichess')->default(false);
            $table->boolean('is_chess_com')->default(false);
            $table->boolean('is_internal')->default(false);
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
