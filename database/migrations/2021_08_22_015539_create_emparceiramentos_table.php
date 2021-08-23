<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmparceiramentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emparceiramentos', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('rodadas_id')->unsigned();
            $table->foreign('rodadas_id')->references("id")->on("rodadas");

            $table->integer('inscricao_a')->unsigned()->nullable(); // Inscrição da posição A
            $table->foreign('inscricao_a')->references("id")->on("inscricao");
            $table->string('numero_a')->nullable(); // Número (ou texto) de posição da posição A (Geralmente utilizado em chave)
            $table->integer('cor_a')->nullable(); // Cor do jogador da posição A - 1 brancas, 2 negras
            $table->decimal('resultado_a',2,1)->nullable(); // Resultado do jogador da posição A
            $table->integer('penalidades_a')->default(0); // Quantidade de penalidades do jogador A

            $table->integer('inscricao_b')->unsigned()->nullable(); // Inscrição da posição B
            $table->foreign('inscricao_b')->references("id")->on("inscricao");
            $table->string('numero_b')->nullable(); // Número (ou texto) de posição da posição B (Geralmente utilizado em chave)
            $table->integer('cor_b')->nullable(); // Cor do jogador da posição B - 1 brancas, 2 negras
            $table->decimal('resultado_b',2,1)->nullable(); // Resultado do jogador da posição B
            $table->integer('penalidades_b')->default(0); // Quantidade de penalidades do jogador B

            /*
             *
             * REGRA DO ARMAGEDDON
             *
             * No caso de desempate em todos os quesitos, geralmente é utilizado o
             * armageddon como desempate, considerando que o sorteio geralmente é
             * algo que leva muito mais em conta a sorte do que de fato o desempenho
             * dos jogadores.
             *
             * A regra consiste em um jogo de xadrez relâmpago com 5 minutos para as
             * brancas e 4 minutos para as negras. Como as brancas tem a vantagem de tempo
             * elas são obrigadas a vencer o jogo, caso ocorra das negras vencerem ou empate,
             * a vitória fica com as negras.
             *
             */
            $table->boolean('is_armageddon')->default(0);

            $table->bigInteger('armageddon_rodadas_id')->unsigned()->nullable();
            $table->foreign('armageddon_rodadas_id')->references("id")->on("rodadas");

            $table->bigInteger('armageddon_emparceiramentos_id')->unsigned()->nullable();
            $table->foreign('armageddon_emparceiramentos_id')->references("id")->on("emparceiramentos");

            // -1 = a
            // 0 = empate
            // 1 = b
            $table->integer('resultado')->nullable();

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
        Schema::dropIfExists('emparceiramentos');
    }
}
