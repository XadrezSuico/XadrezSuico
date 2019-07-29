<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEnxadristaTableAddLBXAndLastUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enxadrista', function (Blueprint $table) {
            $table->integer("lbx_id")->nullable();
            $table->integer("lbx_rating")->nullable();
            $table->datetime("fide_last_update")->nullable();
            $table->datetime("cbx_last_update")->nullable();
            $table->datetime("lbx_last_update")->nullable();
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
