<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClubeTableAddIsFexparVinculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clube', function (Blueprint $table) {
            $table->boolean("is_fexpar___clube_filiado")->default(false);
            $table->boolean("is_fexpar___clube_valido_vinculo_federativo")->default(false);
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
