<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventGroupConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_group_configs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('grupo_evento_id')->unsigned();
            $table->foreign('grupo_evento_id')->references('id')->on("grupo_evento");
            $table->string('key');

            $table->enum('value_type', ["integer", "float", "decimal", "boolean", "string", "date", "datetime"]);

            $table->integer("integer")->nullable();
            $table->float("float")->nullable();
            $table->decimal("decimal", 9, 2)->nullable();
            $table->boolean("boolean")->nullable();
            $table->string("string")->nullable();
            $table->date("date")->nullable();
            $table->datetime("datetime")->nullable();
            $table->json("json")->nullable();
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
        Schema::dropIfExists('event_group_configs');
    }
}
