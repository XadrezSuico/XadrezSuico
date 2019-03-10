<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInscricaoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'inscricao';

    /**
     * Run the migrations.
     * @table inscricao
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('enxadrista_id');
            $table->unsignedInteger('categoria_id');
            $table->unsignedInteger('cidade_id');
            $table->unsignedInteger('clube_id');
            $table->unsignedInteger('torneio_id');
            $table->decimal('pontos', 3, 1)->nullable();
            $table->integer('posicao')->nullable();

            $table->index(["clube_id"], 'fk_inscricao_clube1_idx');

            $table->index(["cidade_id"], 'fk_inscricao_cidade1_idx');

            $table->index(["torneio_id"], 'fk_inscricao_torneio1_idx');

            $table->index(["enxadrista_id"], 'fk_inscricao_enxadrista1_idx');

            $table->index(["categoria_id"], 'fk_inscricao_categoria1_idx');
            $table->timestamps();


            $table->foreign('enxadrista_id', 'fk_inscricao_enxadrista1_idx')
                ->references('id')->on('enxadrista')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('categoria_id', 'fk_inscricao_categoria1_idx')
                ->references('id')->on('categoria')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('cidade_id', 'fk_inscricao_cidade1_idx')
                ->references('id')->on('cidade')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('clube_id', 'fk_inscricao_clube1_idx')
                ->references('id')->on('clube')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('torneio_id', 'fk_inscricao_torneio1_idx')
                ->references('id')->on('torneio')
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
