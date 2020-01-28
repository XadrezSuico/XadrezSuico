<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoDocumentoPaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_documento_pais', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger("tipo_documentos_id")->unsigned();
            $table->foreign("tipo_documentos_id")->references("id")->on("tipo_documentos");
            $table->bigInteger("pais_id")->unsigned();
            $table->foreign("pais_id")->references("id")->on("pais");
            $table->boolean('e_requerido')->default(false);
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
        Schema::dropIfExists('tipo_documento_pais');
    }
}
