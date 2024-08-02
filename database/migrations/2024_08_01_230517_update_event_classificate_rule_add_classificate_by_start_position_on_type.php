<?php

use App\Classification\EventClassificateRule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateEventClassificateRuleAddClassificateByStartPositionOnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Renomear a coluna 'type' para 'type2'
        DB::statement('ALTER TABLE event_classificate_rules CHANGE `type` `type2` ENUM("position", "position-absolute", "pre-classificate", "place-by-quantity")');


        // Adicionar a nova coluna 'type'
        DB::statement("
            ALTER TABLE event_classificate_rules
            ADD COLUMN type ENUM('position', 'position-absolute', 'pre-classificate', 'place-by-quantity', 'classificate-by-start-position')
            AFTER type2
        ");

        EventClassificateRule::all()->each(function ($ecr) {
            $ecr->type = $ecr->type2;
            $ecr->save();
        });

        Schema::table('event_classificate_rules', function (Blueprint $table) {
            $table->dropColumn('type2');
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
