<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Evento;

class UpdateEventoTableAddAllowLayoutRedirect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evento', function (Blueprint $table) {
            $table->boolean("is_allowed_to_layout_redirect")->default(false)->after("e_permite_confirmacao_publica");
        });

        foreach(Evento::all() as $evento){
            $evento->is_allowed_to_layout_redirect = true;
            $evento->save();
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
