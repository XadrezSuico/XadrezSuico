<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\EmailTemplate;
use App\GrupoEvento;
use App\Evento;

class SeedGeralTemplateEmailSevne extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email_template_7 = EmailTemplate::where([
            ["email_type","=",7],
            ["grupo_evento_id","=",null],
            ["evento_id","=",null],
        ])
        ->first();
        if(!$email_template_7){
            $email_template_7 = new EmailTemplate;
            $email_template_7->email_type = 7;
        }
        $email_template_7->name = "Inscrição Recebida - Pagamento Pendente";
        $email_template_7->subject = "{evento.name} - Inscrição Recebida - Pagamento PENDENTE - Enxadrista: {enxadrista.name}";


        $email_template_7->message = "Olá {enxadrista.name}!<br/>";
        $email_template_7->message .= "Você está recebendo este email para informar que recebemos sua inscrição no Evento '{evento.name}' e que a inscrição está com pagamento <strong>pendente</strong>.<br/>";
        $email_template_7->message .= "<strong>Pagamento:</strong><br/>";
        $email_template_7->message .= "Efetue o pagamento da inscrição através do link: <a href='{inscricao.payment.link}' target='_blank'>{inscricao.payment.link}</a>";
        $email_template_7->message .= "<hr/>";
        $email_template_7->message .= "Informações:<br/>";
        $email_template_7->message .= "ID da Inscrição: {inscricao.id}<br/>";
        $email_template_7->message .= "ID do Cadastro de Enxadrista: {enxadrista.id}<br/>";
        $email_template_7->message .= "Cidade: {cidade.name}<br/>";
        $email_template_7->message .= "Clube: {clube.name}<br/>";
        $email_template_7->message .= "Categoria: {categoria.name}";

        $email_template_7->save();


        foreach(GrupoEvento::all() as $grupo_evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",7],
                ["grupo_evento_id","=",$grupo_evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 7;
                $email_template->grupo_evento_id = $grupo_evento->id;
            }
            $email_template->name = $email_template_7->name;
            $email_template->subject = $email_template_7->subject;
            $email_template->message = $email_template_7->message;
            $email_template->save();
        }

        foreach(Evento::all() as $evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",7],
                ["evento_id","=",$evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 7;
                $email_template->evento_id = $evento->id;
            }
            $email_template->name = $email_template_7->name;
            $email_template->subject = $email_template_7->subject;
            $email_template->message = $email_template_7->message;
            $email_template->save();
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
