<?php

use App\EmailTemplate;
use App\Enum\EmailType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedEmailTemplateAddXadrezSuicoClassificadorProcessamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email_template = EmailTemplate::where([["email_type", "=", 10]])->whereNull("grupo_evento_id")->whereNull("evento_id")->first();
        if (!$email_template) {
            $email_template = new EmailTemplate;
            $email_template->email_type = 10;
            $email_template->email_type = EmailType::XadrezSuicoClassificadorProcessamento;
        }
        $email_template->name = "XadrezSuíço Classificador - Relatório de Processamento";
        $email_template->subject = "XadrezSuíço Classificador - Relatório de Processamento - Classificador #{xadrezsuicoclassificador.id}";


        $email_template->message = "Olá {user.name}!<br/>";
        $email_template->message .= "Você está recebendo este email para informar que o Classificador #{xadrezsuicoclassificador.id} foi processado com sucesso!<br/>";
        $email_template->message .= "Informações:<br/>";
        $email_template->message .= "Evento Classificador (de):{xadrezsuicoclassificador.event.from.id} - {xadrezsuicoclassificador.event.from.name}<br/>";
        $email_template->message .= "Evento (para): {xadrezsuicoclassificador.event.to.id} - {xadrezsuicoclassificador.event.to.name}<br/>";
        $email_template->message .= "Log de Processamento<br/>";
        $email_template->message .= "{log}";

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
