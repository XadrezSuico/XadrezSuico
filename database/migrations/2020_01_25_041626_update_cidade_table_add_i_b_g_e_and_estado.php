<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCidadeTableAddIBGEAndEstado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cidade', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer("ibge_id")->nullable()->after("name");
            $table->bigInteger("estados_id")->unsigned()->nullable()->after("name");
            $table->foreign("estados_id")->references("id")->on("estados");
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
