<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerfilUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfil_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references("id")->on("users");
            $table->bigInteger('perfils_id')->unsigned();
            $table->foreign('perfils_id')->references("id")->on("perfils");
            $table->integer('grupo_evento_id')->unsigned()->nullable();
            $table->foreign('grupo_evento_id')->references("id")->on("grupo_evento");
            $table->integer('evento_id')->unsigned()->nullable();
            $table->foreign('evento_id')->references("id")->on("evento");
            $table->boolean('ate_finalizar')->default(false);
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
        Schema::dropIfExists('perfil_users');
    }
}
