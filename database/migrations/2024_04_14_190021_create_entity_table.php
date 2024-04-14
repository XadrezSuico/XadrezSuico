<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(21);
            $table->string('name');
            $table->string('abbr');
            $table->string('website');
            $table->enum('type', [
                "local",
                "state_region",
                "state",
                "national_region",
                "national",
                "world",
                "local_official",
                "state_region_official",
                "state_official",
                "national_region_official",
                "national_official",
                "world_official",
            ])->default("local")->comment("Tipo de categorização da entidade, podendo ser local, regional (estado), estadual, regional (nacional), nacional ou mundial, podendo ser oficial ou não.");
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
        Schema::dropIfExists('entity');
    }
}
