<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubeTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'clube';

    /**
     * Run the migrations.
     * @table clube
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->nullable();
            $table->unsignedInteger('rating_id')->nullable();
            $table->string('name', 45);
            $table->unsignedInteger('cidade_id');

            $table->index(["cidade_id"], 'fk_clube_cidade_idx');
            $table->timestamps();


            $table->foreign('cidade_id', 'fk_clube_cidade_idx')
                ->references('id')->on('cidade')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
