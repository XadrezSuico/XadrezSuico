<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEnxadristaCriterioDesempateGeralTableAddCategoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enxadrista_criterio_desempate_gerals', function (Blueprint $table) {
            $table->integer("categoria_id")->unsigned();
            $table->foreign("categoria_id")->references("id")->on("categoria");
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
