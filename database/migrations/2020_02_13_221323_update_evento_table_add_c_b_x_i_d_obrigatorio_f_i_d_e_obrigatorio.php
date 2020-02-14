<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEventoTableAddCBXIDObrigatorioFIDEObrigatorio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evento', function (Blueprint $table) {
            $table->boolean('fide_required')->default(false)->after("calcula_fide");
            $table->boolean('cbx_required')->default(false)->after("calcula_cbx");
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
