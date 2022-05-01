<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmparceiramentoTableAddRatingsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emparceiramentos', function (Blueprint $table) {

            /*
             *
             * RATING DO JOGADOR A
             *
             */

                // Resultado final da alteração de rating (rating após partida)
                $table->integer("rating_a_final")->nullable()->after("penalidades_a");

                // Movimentação final de resultado (o valor que alterou)
                $table->integer("rating_a_mov")->nullable()->after("penalidades_a");

                // Cálculos de cada tipo de movimentação de acordo com o resultado
                $table->integer("rating_a_if_drw")->nullable()->after("penalidades_a");
                $table->integer("rating_a_if_los")->nullable()->after("penalidades_a");
                $table->integer("rating_a_if_win")->nullable()->after("penalidades_a");

                // Rating inicial para a partida
                $table->integer("rating_a")->nullable()->after("penalidades_b");

            /*
             *
             * RATING DO JOGADOR B
             *
             */

                // Resultado final da alteração de rating (rating após partida)
                $table->integer("rating_b_final")->nullable()->after("penalidades_b");

                // Movimentação final de resultado (o valor que alterou)
                $table->integer("rating_b_mov")->nullable()->after("penalidades_b");

                // Cálculos de cada tipo de movimentação de acordo com o resultado
                $table->integer("rating_b_if_drw")->nullable()->after("penalidades_b");
                $table->integer("rating_b_if_los")->nullable()->after("penalidades_b");
                $table->integer("rating_b_if_win")->nullable()->after("penalidades_b");

                // Rating inicial para a partida
                $table->integer("rating_b")->nullable()->after("penalidades_b");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
