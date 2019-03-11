<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInscricaoCriterioDesempateTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'inscricao_criterio_desempate';

    /**
     * Run the migrations.
     * @table inscricao_criterio_desempate
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->decimal('valor', 3, 2);
            $table->unsignedInteger('inscricao_id');
            $table->unsignedInteger('criterio_desempate_id');

            $table->index(["criterio_desempate_id"], 'fk_inscricao_criterio_desempate_criterio_desempate1_idx');

            $table->index(["inscricao_id"], 'fk_inscricao_criterio_desempate_inscricao1_idx');
            $table->timestamps();


            $table->foreign('inscricao_id', 'fk_inscricao_criterio_desempate_inscricao1_idx')
                ->references('id')->on('inscricao')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('criterio_desempate_id', 'fk_inscricao_criterio_desempate_criterio_desempate1_idx')
                ->references('id')->on('criterio_desempate')
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
