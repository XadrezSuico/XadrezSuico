<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedCriteriosDesempateGeral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Table('criterio_desempate')->insert(array('name' => "Maior Número de 1ºs Lugares", "code" => "1ºs", "is_geral" => 1, "internal_code" => "G1"));
        DB::Table('criterio_desempate')->insert(array('name' => "Maior Número de 2ºs Lugares", "code" => "2ºs", "is_geral" => 1, "internal_code" => "G2"));
        DB::Table('criterio_desempate')->insert(array('name' => "Maior Número de 3ºs Lugares", "code" => "3ºs", "is_geral" => 1, "internal_code" => "G3"));
        DB::Table('criterio_desempate')->insert(array('name' => "Maior Idade", "code" => "Idd", "is_geral" => 1, "internal_code" => "G4"));
        DB::Table('criterio_desempate')->insert(array('name' => "Pontuação Total", "code" => "PtsT", "is_geral" => 1, "internal_code" => "G5"));
        DB::Table('criterio_desempate')->insert(array('name' => "Pontuação Total (Com Corte do Pior Resultado)", "code" => "PtsCP", "is_geral" => 1, "internal_code" => "G6"));
        DB::Table('criterio_desempate')->insert(array('name' => "Pontuação Total (Com Corte do Melhor e Pior Resultado)", "code" => "PtsCMP", "is_geral" => 1, "internal_code" => "G7"));
        DB::Table('criterio_desempate')->insert(array('name' => "Pontuação Média (Considerando o Número Total de Etapas)", "code" => "PtsMeTE", "is_geral" => 1, "internal_code" => "G8"));
        DB::Table('criterio_desempate')->insert(array('name' => "Pontuação Média (Considerando o Número de Etapas Participantes)", "code" => "PtsMeEP", "is_geral" => 1, "internal_code" => "G9"));
        DB::Table('criterio_desempate')->insert(array('name' => "Manual", "code" => "Man", "is_geral" => 1, "internal_code" => "G10","is_manual"=>1));
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
