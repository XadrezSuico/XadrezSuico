<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Vinculo;
use App\VinculoConsulta;


class UpdateVinculoAndVinculoConsultaTableAddConfirmedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vinculos', function (Blueprint $table) {
            $table->datetime("vinculated_at")->nullable();
        });
        Schema::table('vinculo_consultas', function (Blueprint $table) {
            $table->datetime("vinculated_at")->nullable();
        });

        foreach(Vinculo::where([["is_confirmed_system","=",true]])->orWhere([["is_confirmed_manually","=",true]])->get() as $vinculo){
            $vinculo->vinculated_at = date("Y-m-d H:i:s");
            $vinculo->save();


            foreach($vinculo->consultas->all() as $consulta){
                $consulta->vinculated_at = $vinculo->vinculated_at;
                $consulta->save();
            }
        }
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
