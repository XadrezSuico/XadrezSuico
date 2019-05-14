<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnxadristaCriterioDesempateGeralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enxadrista_criterio_desempate_gerals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('valor', 5, 2)->nullable();
            $table->integer('enxadrista_id')->unsigned();
            $table->foreign('enxadrista_id', 'fk_ecsg_enx_id')->references("id")->on("enxadrista");
            $table->integer('grupo_evento_id')->unsigned();
            $table->foreign('grupo_evento_id', 'fk_ecsg_grp_ev_id')->references("id")->on("grupo_evento");
            $table->integer('criterio_desempate_id')->unsigned();
            $table->foreign('criterio_desempate_id', 'fk_ecsg_crt_desm_id')->references("id")->on("criterio_desempate");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enxadrista_criterio_desempate_gerals');
    }
}
