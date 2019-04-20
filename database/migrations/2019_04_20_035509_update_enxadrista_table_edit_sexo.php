<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEnxadristaTableEditSexo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("enxadrista", function (Blueprint $table) {
            $table->dropColumn("sexo");
            $table->integer('sexos_id')->unsigned()->nullable();
            $table->foreign("sexos_id")->references("id")->on("sexos");
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
