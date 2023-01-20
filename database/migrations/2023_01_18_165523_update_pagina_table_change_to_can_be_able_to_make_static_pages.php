<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaginaTableChangeToCanBeAbleToMakeStaticPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paginas', function (Blueprint $table) {
            $table->integer("evento_id")->unsigned()->nullable()->change();
        });
        Schema::table('paginas', function (Blueprint $table) {
            $table->string("title")->nullable();
            $table->uuid("uuid")->after("id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('can_be_able_to_make_static_pages', function (Blueprint $table) {
            //
        });
    }
}
