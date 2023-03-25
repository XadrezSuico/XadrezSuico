<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedCriterioDesempateAddTA1TA2TA3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Table('criterio_desempate')->insert(array('name' => "Maior Número de 1ºs Lugares", "code" => "1ºs", "is_team_award" => 1, "internal_code" => "TA1","is_sm"=>false));
        DB::Table('criterio_desempate')->insert(array('name' => "Maior Número de 2ºs Lugares", "code" => "2ºs", "is_team_award" => 1, "internal_code" => "TA2","is_sm"=>false));
        DB::Table('criterio_desempate')->insert(array('name' => "Maior Número de 3ºs Lugares", "code" => "3ºs", "is_team_award" => 1, "internal_code" => "TA3","is_sm"=>false));
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
