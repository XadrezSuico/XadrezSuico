<?php

use App\Enum\RegistrationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToInscricaoTable extends Migration
{
    public function up()
    {
        Schema::table('inscricao', function (Blueprint $table) {
            $table->enum('status', [RegistrationStatus::Enabled, RegistrationStatus::Disabled, RegistrationStatus::Deleted])
                ->default(RegistrationStatus::Enabled)->index();
        });
    }

    public function down()
    {
        Schema::table('inscricao', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
