<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedCriteriosDesempateSMFrom72To90 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Table('criterio_desempate')->insert(array('name' => "Only available in Swiss-Chess", "code" => null, "sm_code" => "72", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Only available in Swiss-Chess", "code" => null, "sm_code" => "73", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Olympiad-Sonneborn-Berger-Tie-Break without lowest result (Chennai)", "code" => null, "sm_code" => "74", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Olympiad-Sum of Adjusted matchpoints without lowest result (Chennai)", "code" => null, "sm_code" => "75", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Game-Points Variable for white and black", "code" => null, "sm_code" => "76", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Average of Opponents Buchholz (AOB)", "code" => "AOB", "sm_code" => "77", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Board count (BC)", "code" => "BC", "sm_code" => "78", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Rounds Elected to Play (REP)", "code" => "REP", "sm_code" => "79", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Average Rating of Opponents (ARO)", "code" => "ARO", "sm_code" => "80", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Direct Encounter DE (DE)", "code" => "DE", "sm_code" => "81", "direction" => "ASC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Extended Sonneborn-Berger for teams (ESB)", "code" => "ESB", "sm_code" => "82", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Extended Direct Encounter for teams (EDE)", "code" => "EDE", "sm_code" => "83", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Buchholz (BH)", "code" => "BH", "sm_code" => "84", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Sonneborn berger (SB)", "code" => "SB", "sm_code" => "85", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Progressive Score (PS)", "code" => "PS", "sm_code" => "86", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Koya System (KS)", "code" => "KS", "sm_code" => "87", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Performance Tie-Breaks", "code" => null, "sm_code" => "88", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Top Board Results (TBR)", "code" => "TBR", "sm_code" => "89", "direction" => "DESC"));
        DB::Table('criterio_desempate')->insert(array('name' => "Bottom Board Elimination (BBE)", "code" => "BBE", "sm_code" => "90", "direction" => "DESC"));
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
