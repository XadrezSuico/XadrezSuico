<?php

use App\Entity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedEntityTableAdd0FIDE0CBX1LBX2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $entities = [
            [1, "Fédération Internationale des Échecs", "FIDE", "https://www.fide.com", "world_official"],
            [2, "Confederação Brasileira de Xadrez", "CBX", "https://www.cbx.com.br", "national_official"],
            [3, "Liga Brasileira de Xadrez", "LBX", "https://www.lbx.org.br", "national"],
        ];

        foreach ($entities as $ent) {
            if (Entity::where([["id","=", $ent[0]]])->count() > 0){
                $entity = Entity::where([["id", "=", $ent[0]]])->first();
            }else{
                $entity = new Entity;
                $entity->id = $ent[0];
            }

            $entity->name = $ent[1];
            $entity->abbr = $ent[2];
            $entity->website = $ent[3];
            $entity->type = $ent[4];
            $entity->save();
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
