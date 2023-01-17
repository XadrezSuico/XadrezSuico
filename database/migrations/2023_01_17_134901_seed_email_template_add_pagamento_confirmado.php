<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\EmailTemplate;
use App\Enum\EmailType;

use App\GrupoEvento;
use App\Evento;

class SeedEmailTemplateAddPagamentoConfirmado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email_template = EmailTemplate::where([["email_type","=",6]])
        ->whereNull("grupo_evento_id")
        ->whereNull("evento_id")
        ->first();
        if(!$email_template){
            $email_template = new EmailTemplate;
            $email_template->email_type = 6;
        }
        $email_template->name = "Pagamento Confirmado";
        $email_template->subject = "{evento.name} - Pagamento Confirmado - Enxadrista: {enxadrista.name}";


        $email_template->message = "Olá {enxadrista.name}!<br/>";
        $email_template->message .= "Você está recebendo este email para informar que o PAGAMENTO para a inscrição abaixo para o Evento '{evento.name}' <strong>foi confirmado.</strong><br/>";
        $email_template->message .= "Informações:<br/>";
        $email_template->message .= "ID da Inscrição: {inscricao.uuid}<br/>";
        $email_template->message .= "ID do Cadastro de Enxadrista: {enxadrista.id}<br/>";
        $email_template->message .= "Cidade: {cidade.name}<br/>";
        $email_template->message .= "Clube: {clube.name}<br/>";
        $email_template->message .= "Categoria: {categoria.name}";

        $email_template->save();

        foreach(GrupoEvento::all() as $grupo_evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",6],
                ["grupo_evento_id","=",$grupo_evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 6;
                $email_template->grupo_evento_id = $grupo_evento->id;
            }
            $email_template->name = "Pagamento Confirmado";
            $email_template->subject = "{evento.name} - Pagamento Confirmado - Enxadrista: {enxadrista.name}";


            $email_template->message = "Olá {enxadrista.name}!<br/>";
            $email_template->message .= "Você está recebendo este email para informar que o PAGAMENTO para a inscrição abaixo para o Evento '{evento.name}' <strong>foi confirmado.</strong><br/>";
            $email_template->message .= "Informações:<br/>";
            $email_template->message .= "ID da Inscrição: {inscricao.uuid}<br/>";
            $email_template->message .= "ID do Cadastro de Enxadrista: {enxadrista.id}<br/>";
            $email_template->message .= "Cidade: {cidade.name}<br/>";
            $email_template->message .= "Clube: {clube.name}<br/>";
            $email_template->message .= "Categoria: {categoria.name}";

            $email_template->save();
        }

        foreach(Evento::all() as $evento){
            $email_template = null;

            $email_template = EmailTemplate::where([
                ["email_type","=",6],
                ["grupo_evento_id","=",$evento->id],
            ])
            ->first();
            if(!$email_template){
                $email_template = new EmailTemplate;
                $email_template->email_type = 6;
                $email_template->evento_id = $evento->id;
            }
            $email_template->name = "Pagamento Confirmado";
            $email_template->subject = "{evento.name} - Pagamento Confirmado - Enxadrista: {enxadrista.name}";


            $email_template->message = "Olá {enxadrista.name}!<br/>";
            $email_template->message .= "Você está recebendo este email para informar que o PAGAMENTO para a inscrição abaixo para o Evento '{evento.name}' <strong>foi confirmado.</strong><br/>";
            $email_template->message .= "Informações:<br/>";
            $email_template->message .= "ID da Inscrição: {inscricao.uuid}<br/>";
            $email_template->message .= "ID do Cadastro de Enxadrista: {enxadrista.id}<br/>";
            $email_template->message .= "Cidade: {cidade.name}<br/>";
            $email_template->message .= "Clube: {clube.name}<br/>";
            $email_template->message .= "Categoria: {categoria.name}";

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
