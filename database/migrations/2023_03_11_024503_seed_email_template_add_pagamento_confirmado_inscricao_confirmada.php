<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\EmailTemplate;
use App\Enum\EmailType;

use App\GrupoEvento;
use App\Evento;

class SeedEmailTemplateAddPagamentoConfirmadoInscricaoConfirmada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email_template_8 = EmailTemplate::where([["email_type","=",8]])->whereNull("grupo_evento_id")->whereNull("evento_id")->first();
        if(!$email_template_8){
            $email_template_8 = new EmailTemplate;
            $email_template_8->email_type = 8;
        }
        $email_template_8->name = "Pagamento Confirmado - Inscrição Confirmada";
        $email_template_8->subject = "Pagamento Confirmado - Inscrição Confirmada - {evento.name} - Enxadrista: {enxadrista.name}";


        $email_template_8->message = "Olá {enxadrista.name}!<br/>";
        $email_template_8->message .= "Você está recebendo este email para informar que o PAGAMENTO para a inscrição abaixo para o Evento '{evento.name}' <strong>foi confirmado.</strong><br/>";
        $email_template_8->message .= "Informamos também que sua inscrição foi confirmada <strong>AUTOMATICAMENTE</strong> através do seu pagamento, não necessitando efetuar confirmação de presença no local.<hr/>";
        $email_template_8->message .= "Informações:<br/>";
        $email_template_8->message .= "ID da Inscrição: {inscricao.id}<br/>";
        $email_template_8->message .= "ID do Cadastro de Enxadrista: {enxadrista.id}<br/>";
        $email_template_8->message .= "Cidade: {cidade.name}<br/>";
        $email_template_8->message .= "Clube: {clube.name}<br/>";
        $email_template_8->message .= "Categoria: {categoria.name}";

        $email_template_8->save();


        foreach(GrupoEvento::all() as $grupo_evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",8],
                ["grupo_evento_id","=",$grupo_evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 8;
                $email_template->grupo_evento_id = $grupo_evento->id;
            }
            $email_template->name = $email_template_8->name;
            $email_template->subject = $email_template_8->subject;
            $email_template->message = $email_template_8->message;
            $email_template->save();
        }

        foreach(Evento::all() as $evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",8],
                ["evento_id","=",$evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 8;
                $email_template->evento_id = $evento->id;
            }
            $email_template->name = $email_template_8->name;
            $email_template->subject = $email_template_8->subject;
            $email_template->message = $email_template_8->message;
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
