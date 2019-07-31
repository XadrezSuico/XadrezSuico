<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Software;

class SeedSoftwareAddSwissManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $software_1 = Software::find(1);
        if(!$software_1){
            $software_1 = new Software;
            $software_1->id = 1;
        }
        $software_1->name = "Swiss-Manager";
        $software_1->save();
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
