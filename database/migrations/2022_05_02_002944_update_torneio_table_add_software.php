<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTorneioTableAddSoftware extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('torneio', function (Blueprint $table) {
            $table->integer("softwares_id")->unsigned()->default(1)->after("torneio_template_id");
            $table->foreign("softwares_id")->references("id")->on("softwares");
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
