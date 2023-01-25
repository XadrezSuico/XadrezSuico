<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTimelineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_timeline_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references("id")->on("evento");
            $table->integer("order")->default(1)->unsigned();
            $table->string("title");
            $table->boolean("is_expected");
            $table->datetime("datetime");
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
        Schema::dropIfExists('event_timeline_items');
    }
}
