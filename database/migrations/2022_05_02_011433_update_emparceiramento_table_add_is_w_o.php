<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmparceiramentoTableAddIsWO extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emparceiramentos', function (Blueprint $table) {

            // Se A foi W.O.
            $table->boolean("is_wo_a")->default(false)->after("penalidades_a");
            // Se A foi W.O.
            $table->boolean("is_wo_b")->default(false)->after("penalidades_b");

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
