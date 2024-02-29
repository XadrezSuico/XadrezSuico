<?php

use App\EventConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventConfigTableAddDateDatetimeEnums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Schema::table("event_configs", function (Blueprint $table) {
            $table->enum('value_type_new', ["integer", "float", "decimal", "boolean", "string", "date", "datetime"])->after("value_type");
        });

        foreach(EventConfig::all() as $event_config){
            $event_config->value_type_new = $event_config->value_type;
            $event_config->save();
        }
        Schema::table("event_configs", function (Blueprint $table) {
            $table->dropColumn('value_type');
        });
        Schema::table("event_configs", function (Blueprint $table) {
            $table->renameColumn('value_type_new', 'value_type');
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
