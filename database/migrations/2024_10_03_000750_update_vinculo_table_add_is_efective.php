<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVinculoTableAddIsEfective extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("vinculos", function (Blueprint $table) {
            $table->boolean('is_efective')->default(false)->after("obs")->comment("Indica que este vínculo já foi um vínculo confirmado em outro ano.");
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
