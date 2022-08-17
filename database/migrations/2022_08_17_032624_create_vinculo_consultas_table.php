<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVinculoConsultasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vinculo_consultas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->ipAddress('ip');
            $table->uuid('vinculos_uuid');
            $table->integer('vinculos_id')->unsigned();
            $table->integer('enxadrista_id')->unsigned();

            $table->year('ano');

            $table->integer('cidade_id')->unsigned();
            $table->integer('clube_id')->unsigned();

            $table->boolean("is_confirmed_system")->default(false);
            $table->boolean("is_confirmed_manually")->default(false);

            $table->integer("system_inscricoes_in_this_club_confirmed")->default(0);

            $table->text("events_played")->nullable();

            $table->text("obs")->nullable();

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
        Schema::dropIfExists('vinculo_consultas');
    }
}
