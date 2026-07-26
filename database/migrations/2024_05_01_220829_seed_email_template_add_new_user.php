<?php

use App\EmailTemplate;
use App\Enum\EmailType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedEmailTemplateAddNewUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type_id = EmailType::XadrezSuicoClassificadorProcessamento;
        $email_template = EmailTemplate::where([["email_type", "=", $type_id]])->whereNull("grupo_evento_id")->whereNull("evento_id")->first();
        if (!$email_template) {
            $email_template = new EmailTemplate;
            $email_template->email_type = $type_id;
        }
        $email_template->name = "XadrezSuíço - Novo Usuário";
        $email_template->subject = "XadrezSuíço - Novo Usuário";


        $email_template->message = "Olá {user.name}!<br/>";
        $email_template->message .= "Você está recebendo este email para informar que foi criado um usuário para você no XadrezSuíço.<br/>";
        $email_template->message .= "Informações:<br/>";
        $email_template->message .= "E-mail: {user.email}";
        $email_template->message .= "Senha Inicial: {user.password}";

        $email_template->save();
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
