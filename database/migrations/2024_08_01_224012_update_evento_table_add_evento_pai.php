<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventoTableAddEventoPai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("evento", function (Blueprint $table) {
            $table->integer('parent_evento_id')->unsigned()->nullable()->after("uuid");
            $table->foreign('parent_evento_id')->references("id")->on("evento");
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
