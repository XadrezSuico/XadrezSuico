<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedCriteriosDesempateGerais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Table('criterio_desempate')->insert(array('name' => "Melhor posição na primeira etapa", "code" => "MP-1", "is_geral" => 1, "internal_code" => "G11", "direction" => "ASC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Melhor posição na segunda etapa", "code" => "MP-2", "is_geral" => 1, "internal_code" => "G12", "direction" => "ASC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Melhor posição na terceira etapa", "code" => "MP-3", "is_geral" => 1, "internal_code" => "G13", "direction" => "ASC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Melhor posição na quarta etapa", "code" => "MP-4", "is_geral" => 1, "internal_code" => "G14", "direction" => "ASC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Melhor posição na quinta etapa", "code" => "MP-5", "is_geral" => 1, "internal_code" => "G15", "direction" => "ASC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Quantidade de etapas participantes", "code" => "QTP", "is_geral" => 1, "internal_code" => "G16"));
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
