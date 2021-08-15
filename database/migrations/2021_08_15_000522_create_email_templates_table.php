<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('subject');
            $table->longText('message');
            $table->integer('grupo_evento_id')->unsigned()->nullable();
            $table->foreign('grupo_evento_id')->references("id")->on("grupo_evento");
            $table->integer('evento_id')->unsigned()->nullable();
            $table->foreign('evento_id')->references("id")->on("evento");
            $table->integer('email_type')->default(1);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('email_templates');
    }
}
