<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClassificacaoGeralFields extends Migration
{
    public function up()
    {
        Schema::table('inscricao', function (Blueprint $table) {
            $table->decimal('pontos_classificacao_geral', 5, 2)->nullable()->after('pontos_geral');
            $table->integer('posicao_classificacao_geral')->nullable()->after('posicao_geral');
        });

        Schema::table('enxadrista_criterio_desempate_gerals', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
        });

        Schema::table('enxadrista_criterio_desempate_gerals', function (Blueprint $table) {
            $table->integer('categoria_id')->unsigned()->nullable()->change();
            $table->foreign('categoria_id')->references('id')->on('categoria');
        });
    }

    public function down()
    {
        Schema::table('inscricao', function (Blueprint $table) {
            $table->dropColumn(['pontos_classificacao_geral', 'posicao_classificacao_geral']);
        });

        Schema::table('enxadrista_criterio_desempate_gerals', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
        });

        Schema::table('enxadrista_criterio_desempate_gerals', function (Blueprint $table) {
            $table->integer('categoria_id')->unsigned()->nullable(false)->change();
            $table->foreign('categoria_id')->references('id')->on('categoria');
        });
    }
}
