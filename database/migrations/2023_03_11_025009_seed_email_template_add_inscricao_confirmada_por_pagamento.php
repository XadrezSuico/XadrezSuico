<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\EmailTemplate;
use App\Enum\EmailType;

use App\GrupoEvento;
use App\Evento;

class SeedEmailTemplateAddInscricaoConfirmadaPorPagamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email_template_9 = EmailTemplate::where([["email_type","=",9]])->whereNull("grupo_evento_id")->whereNull("evento_id")->first();
        if(!$email_template_9){
            $email_template_9 = new EmailTemplate;
            $email_template_9->email_type = 9;
        }
        $email_template_9->name = "Inscrição Confirmada AUTOMATICAMENTE por Pagamento";
        $email_template_9->subject = "Inscrição Confirmada AUTOMATICAMENTE por Pagamento - {evento.name} - Enxadrista: {enxadrista.name}";


        $email_template_9->message = "Olá {enxadrista.name}!<br/>";
        $email_template_9->message .= "Você está recebendo este email para informar que a sua presença o Evento '{evento.name}' <strong>foi confirmada AUTOMATICAMENTE.</strong><br/>";
        $email_template_9->message .= "A confirmação foi efetuada automaticamente pelo pagamento efetuado de sua inscrição. Não há a necessidade de confirmação de presença no dia.<hr/>";
        $email_template_9->message .= "Informações:<br/>";
        $email_template_9->message .= "ID da Inscrição: {inscricao.id}<br/>";
        $email_template_9->message .= "ID do Cadastro de Enxadrista: {enxadrista.id}<br/>";
        $email_template_9->message .= "Cidade: {cidade.name}<br/>";
        $email_template_9->message .= "Clube: {clube.name}<br/>";
        $email_template_9->message .= "Categoria: {categoria.name}";

        $email_template_9->save();


        foreach(GrupoEvento::all() as $grupo_evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",9],
                ["grupo_evento_id","=",$grupo_evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 9;
                $email_template->grupo_evento_id = $grupo_evento->id;
            }
            $email_template->name = $email_template_9->name;
            $email_template->subject = $email_template_9->subject;
            $email_template->message = $email_template_9->message;
            $email_template->save();
        }

        foreach(Evento::all() as $evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",9],
                ["evento_id","=",$evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 9;
                $email_template->evento_id = $evento->id;
            }
            $email_template->name = $email_template_9->name;
            $email_template->subject = $email_template_9->subject;
            $email_template->message = $email_template_9->message;
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
