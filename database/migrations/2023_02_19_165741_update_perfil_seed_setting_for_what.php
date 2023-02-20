<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Perfil;

class UpdatePerfilSeedSettingForWhat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $profiles = [
            ["id"=>3,"is_for"=>"event"],
            ["id"=>4,"is_for"=>"event"],
            ["id"=>5,"is_for"=>"event"],
            ["id"=>6,"is_for"=>"event_group"],
            ["id"=>7,"is_for"=>"event_group"],
        ];

        foreach($profiles as $profile){
            if(Perfil::where([["id","=",$profile["id"]]])->count() > 0){
                $perfil = Perfil::find($profile["id"]);
                switch($profile["is_for"]){
                    case "event":
                        $perfil->is_for_event = true;
                        $perfil->save();
                        break;
                    case "event_group":
                        $perfil->is_for_event_group = true;
                        $perfil->save();
                        break;
                }
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
