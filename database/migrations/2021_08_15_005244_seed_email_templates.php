<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\EmailTemplate;
use App\Enum\EmailType;

class SeedEmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email_template_1 = EmailTemplate::find(1);
        if(!$email_template_1){
            $email_template_1 = new EmailTemplate;
            $email_template_1->id = 1;
            $email_template_1->email_type = 1;
        }
        $email_template_1->name = "Confirmação de Cadastro de Enxadrista";
        $email_template_1->subject = "Sistema XadrezSuíço - Cadastro de Enxadrista Realizado - Enxadrista: {enxadrista.name}";

        $email_template_1->message = "Olá {enxadrista.name}!<br/>";
        $email_template_1->message .= "Esta é uma confirmação do seu cadastro no Sistema XadrezSuíço<br/>";
        $email_template_1->message .= "O seu ID de Cadastro é <strong><u>{enxadrista.id}</u></strong> e você poderá utilizar ele para encontrar seu cadastro para inscrição no Sistema XadrezSuíço e também para poder efetuar a sua confirmação nos eventos que foi utilizado esta implementação do sistema.<br/>";
        $email_template_1->message .= "Recomendamos que você mantenha salvo este ID/Código de Cadastro para poder agilizar o processo de confirmação ou inscrição.<br/>";
        $email_template_1->message .= "Além disso, você receberá neste e-mail as confirmações de inscrições efetuadas nesta implementação do Sistema XadrezSuíço.<br/>";
        $email_template_1->message .= "Atenciosamente.";

        $email_template_1->save();


        $email_template_2 = EmailTemplate::find(2);
        if(!$email_template_2){
            $email_template_2 = new EmailTemplate;
            $email_template_2->id = 2;
            $email_template_2->email_type = 2;
        }
        $email_template_2->name = "Confirmação de Inscrição";
        $email_template_2->subject = "{evento.name} - Inscrição Recebida - Enxadrista: {enxadrista.name}";


        $email_template_2->message = "Olá {enxadrista.name}!<br/>";
        $email_template_2->message .= "Você está recebendo este email para confirmar a inscrição no Evento '{evento.name}'.<br/>";
        $email_template_2->message .= "Informações:<br/>";
        $email_template_2->message .= "ID da Inscrição: {inscricao.id}<br/>";
        $email_template_2->message .= "ID do Cadastro de Enxadrista: {enxadrista.id}<br/>";
        $email_template_2->message .= "Cidade: {cidade.name}<br/>";
        $email_template_2->message .= "Clube: {clube.name}<br/>";
        $email_template_2->message .= "Categoria: {categoria.name}";

        $email_template_2->save();


        $email_template_3 = EmailTemplate::find(3);
        if(!$email_template_3){
            $email_template_3 = new EmailTemplate;
            $email_template_3->id = 3;
            $email_template_3->email_type = 3;
        }
        $email_template_3->name = "Confirmação de Inscrição no Lichess.org";
        $email_template_3->subject = "{evento.name} - Inscrição Completa - Enxadrista: {enxadrista.name}";

        $email_template_3->message = "Olá {enxadrista.name}!<br/>";
        $email_template_3->message .= "Você está recebendo este email para pois efetuou inscrição no Evento '{evento.name}', e sua <strong>inscrição foi confirmada no Lichess.org</strong>.<br/>";
        $email_template_3->message .= "Lembrando que é necessário que no horário do torneio esteja logado no Lichess.org e esteja com o torneio aberto: Segue link para facilitar o acesso: <a href=\"{evento.lichess.tournament}\">{evento.lichess.tournament}</a>.<br/>";

        $email_template_3->save();


        $email_template_4 = EmailTemplate::find(4);
        if(!$email_template_4){
            $email_template_4 = new EmailTemplate;
            $email_template_4->id = 4;
            $email_template_4->email_type = 4;
        }
        $email_template_4->name = "Aviso de Necessidade de Inscrição no Torneio no Lichess.org";
        $email_template_4->subject = "{evento.name} - IMPORTANTE! - Inscrição no Torneio do Lichess.org - Enxadrista: {enxadrista.name}";

        $email_template_4->message = "Olá {enxadrista.name}!<br/>";
        $email_template_4->message .= "Você está recebendo este email para pois efetuou inscrição no Evento '{evento.name}', porém <strong>ainda não se inscreveu no torneio do Lichess.org</strong>.<br/>";
        $email_template_4->message .= "Você necessita efetuar a inscrição, pois sem efetuar a inscrição junto ao Torneio do Lichess.org, você não poderá jogar o torneio e inclusive terá sua inscrição cancelada.<br/>";
        $email_template_4->message .= "O processo é simples: Entre na Equipe do Evento no Lichess.org ({evento.lichess.team}) e depois se inscreva no Torneio ({evento.lichess.tournament}).<br/>";
        $email_template_4->message .= "Lembre-se: Você tem até {evento.fim_inscricoes} para efetuar estes passos, pois senão terá sua inscrição cancelada e não poderá jogar o evento.<br/>";

        $email_template_4->save();

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
