<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('subject');
            $table->text('text');
            $table->boolean('is_sent')->default(false);
            $table->datetime('sent_at')->nullable();
			$table->integer('enxadrista_id')->unsigned()->nullable();
			$table->foreign('enxadrista_id')->references('id')->on('enxadrista');
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
        Schema::dropIfExists('emails');
    }
}
