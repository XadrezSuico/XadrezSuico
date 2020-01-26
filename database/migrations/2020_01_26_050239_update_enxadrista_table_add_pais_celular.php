<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEnxadristaTableAddPaisCelular extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enxadrista', function (Blueprint $table) {
            $table->bigInteger('pais_celular_id')->unsigned()->nullable()->after("sexos_id");
            $table->foreign('pais_celular_id')->references("id")->on("pais");
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
