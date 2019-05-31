<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampoPersonalizadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campo_personalizados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('question')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('validator')->nullable();
            $table->string('mask')->nullable();
            $table->string('type')->nullable();
            $table->string('data_type')->nullable();
            $table->boolean('is_required')->default(false);
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
        Schema::dropIfExists('campo_personalizados');
    }
}
