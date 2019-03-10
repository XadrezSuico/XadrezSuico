<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnxadristaTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'enxadrista';

    /**
     * Run the migrations.
     * @table enxadrista
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('rating_id')->nullable();
            $table->string('name', 200);
            $table->date('born');
            $table->unsignedInteger('cidade_id');
            $table->unsignedInteger('clube_id');

            $table->index(["clube_id"], 'fk_enxadrista_clube1_idx');

            $table->index(["cidade_id"], 'fk_enxadrista_cidade1_idx');
            $table->timestamps();


            $table->foreign('cidade_id', 'fk_enxadrista_cidade1_idx')
                ->references('id')->on('cidade')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('clube_id', 'fk_enxadrista_clube1_idx')
                ->references('id')->on('clube')
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
