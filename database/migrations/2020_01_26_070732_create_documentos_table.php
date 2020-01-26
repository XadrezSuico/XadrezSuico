<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero');
            $table->bigInteger("tipo_documentos_id")->unsigned();
            $table->foreign("tipo_documentos_id")->references("id")->on("tipo_documentos");
            $table->integer("enxadrista_id")->unsigned();
            $table->foreign("enxadrista_id")->references("id")->on("enxadrista");
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
        Schema::dropIfExists('documentos');
    }
}
