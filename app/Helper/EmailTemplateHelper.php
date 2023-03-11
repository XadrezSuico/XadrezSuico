<?php
namespace App\Helper;

use App\GrupoEvento;
use App\Evento;
use App\EmailTemplate;

use App\Enum\EmailType;


class EmailTemplateHelper
{
    public function generate($email_type, $object){
        switch($email_type){
            case EmailType::CadastroEnxadrista:
                return $this->generateCadastroEnxadrista($object);
                break;
            case EmailType::ConfirmacaoInscricao:
                return $this->generateConfirmacaoInscricao($object);
                break;
            case EmailType::ConfirmacaoInscricaoLichess:
                return $this->generateConfirmacaoInscricaoLichess($object);
                break;
            case EmailType::AvisoNecessidadeInscricaoLichess:
                return $this->generateAvisoNecessidadeInscricaoLichess($object);
                break;
            case EmailType::InscricaoConfirmada:
                return $this->generateInscricaoConfirmada($object);
                break;
            case EmailType::PagamentoConfirmado:
                return $this->generatePagamentoConfirmado($object);
                break;
            case EmailType::InscricaoRecebidaPagamentoPendente:
                return $this->generateInscricaoRecebidaPagamentoPendente($object);
                break;
            case EmailType::PagamentoConfirmadoInscricaoConfirmada:
                return $this->generatePagamentoConfirmadoInscricaoConfirmada($object);
                break;
            case EmailType::InscricaoConfirmadaComPagamentoAutomatico:
                return $this->generateInscricaoConfirmadaComPagamentoAutomatico($object);
                break;
        }
    }

    private function generateCadastroEnxadrista($enxadrista){
        // Busca Template de E-mail Geral
        $email_template = EmailTemplate::where([["email_type","=",EmailType::CadastroEnxadrista]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();

        $email_template->subject = $this->fillEnxadristaFields($email_template->subject,$enxadrista);
        $email_template->message = $this->fillEnxadristaFields($email_template->message,$enxadrista);

        return $email_template;
    }

    private function generateConfirmacaoInscricao($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricao]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricao]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricao]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricao]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::ConfirmacaoInscricao]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }

    private function generateConfirmacaoInscricaoLichess($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricaoLichess]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricaoLichess]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricaoLichess]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::ConfirmacaoInscricaoLichess]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::ConfirmacaoInscricaoLichess]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }

    private function generateAvisoNecessidadeInscricaoLichess($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::AvisoNecessidadeInscricaoLichess]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::AvisoNecessidadeInscricaoLichess]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::AvisoNecessidadeInscricaoLichess]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::AvisoNecessidadeInscricaoLichess]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::AvisoNecessidadeInscricaoLichess]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }

    private function generateInscricaoConfirmada($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmada]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmada]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmada]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmada]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::InscricaoConfirmada]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }

    private function generatePagamentoConfirmado($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmado]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmado]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmado]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmado]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::PagamentoConfirmado]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }

    private function generateInscricaoRecebidaPagamentoPendente($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::InscricaoRecebidaPagamentoPendente]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::InscricaoRecebidaPagamentoPendente]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::InscricaoRecebidaPagamentoPendente]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::InscricaoRecebidaPagamentoPendente]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::InscricaoRecebidaPagamentoPendente]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }

    private function generatePagamentoConfirmadoInscricaoConfirmada($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmadoInscricaoConfirmada]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmadoInscricaoConfirmada]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmadoInscricaoConfirmada]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::PagamentoConfirmadoInscricaoConfirmada]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::PagamentoConfirmadoInscricaoConfirmada]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }

    private function generateInscricaoConfirmadaComPagamentoAutomatico($inscricao){
        $email_template = NULL;
        // Busca Template de E-mail no Evento
        if($inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmadaComPagamentoAutomatico]])->count() > 0){
            $email_template = $inscricao->torneio->evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmadaComPagamentoAutomatico]])->first();
        }else
            // Busca Template de E-mail no Grupo de Evento
            if($inscricao->torneio->evento->grupo_evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmadaComPagamentoAutomatico]])->count() > 0){
                $email_template = $inscricao->torneio->grupo_evento->email_templates()->where([["email_type","=",EmailType::InscricaoConfirmadaComPagamentoAutomatico]])->first();
            }else{
                // Busca Template de E-mail Geral
                $email_template = EmailTemplate::where([["email_type","=",EmailType::InscricaoConfirmadaComPagamentoAutomatico]])->whereNull("evento_id")->whereNull("grupo_evento_id")->first();
            }
        $email_template->subject = $this->fillInscricaoFields($email_template->subject,$inscricao);
        $email_template->message = $this->fillInscricaoFields($email_template->message,$inscricao);

        return $email_template;
    }



    private function fillInscricaoFields($text, $inscricao){
        // Inscrição
        $text = str_replace("{inscricao.id}",$inscricao->id,$text);
        $text = str_replace("{inscricao.uuid}",$inscricao->uuid,$text);
        $text = str_replace("{inscricao.lichess}",$inscricao->getLichessProcessLink(),$text);

        if($inscricao->torneio->evento->permite_edicao_inscricao){
            $text = str_replace("{inscricao.link_edicao}",url("/inscricao/".$inscricao->uuid."/editar"),$text);
        }

        // Pagamento
        if($inscricao->payment_info){
            $text = str_replace("{inscricao.payment.uuid}",$inscricao->getPaymentInfo("uuid"),$text);
            $text = str_replace("{inscricao.payment.link}",$inscricao->getPaymentInfo("link"),$text);
        }

        // Categoria
        $text = str_replace("{categoria.id}",$inscricao->categoria->id,$text);
        $text = str_replace("{categoria.name}",$inscricao->categoria->name,$text);

        // Cidade
        $text = str_replace("{cidade.id}",$inscricao->cidade->id,$text);
        $text = str_replace("{cidade.name}",$inscricao->cidade->name,$text);
        // Cidade -> Estado
        $text = str_replace("{cidade.estado.id}",$inscricao->cidade->estado->id,$text);
        $text = str_replace("{cidade.estado.name}",$inscricao->cidade->estado->nome,$text);
        $text = str_replace("{cidade.estado.uf}",$inscricao->cidade->estado->abbr,$text);
        // Cidade -> Estado -> País
        $text = str_replace("{cidade.estado.pais.id}",$inscricao->cidade->estado->pais->id,$text);
        $text = str_replace("{cidade.estado.pais.name}",$inscricao->cidade->estado->pais->nome,$text);
        $text = str_replace("{cidade.estado.pais.iso}",$inscricao->cidade->estado->pais->codigo_iso,$text);

        if($inscricao->clube){
            // Clube
            $text = str_replace("{clube.id}",$inscricao->clube->id,$text);
            $text = str_replace("{clube.name}",$inscricao->clube->name,$text);
            // Clube -> Cidade
            $text = str_replace("{clube.cidade.id}",$inscricao->clube->cidade->id,$text);
            $text = str_replace("{clube.cidade.name}",$inscricao->clube->cidade->name,$text);
            // Clube -> Cidade -> Estado
            $text = str_replace("{clube.cidade.estado.id}",$inscricao->clube->cidade->estado->id,$text);
            $text = str_replace("{clube.cidade.estado.name}",$inscricao->clube->cidade->estado->nome,$text);
            $text = str_replace("{clube.cidade.estado.uf}",$inscricao->clube->cidade->estado->abbr,$text);
            // Clube -> Cidade -> Estado -> País
            $text = str_replace("{clube.cidade.estado.pais.id}",$inscricao->clube->cidade->estado->pais->id,$text);
            $text = str_replace("{clube.cidade.estado.pais.name}",$inscricao->clube->cidade->estado->pais->nome,$text);
            $text = str_replace("{clube.cidade.estado.pais.iso}",$inscricao->clube->cidade->estado->pais->codigo_iso,$text);
        }else{
            // Clube
            $text = str_replace("{clube.id}","-",$text);
            $text = str_replace("{clube.name}","Sem Clube",$text);
            // Clube -> Cidade
            $text = str_replace("{clube.cidade.id}","-",$text);
            $text = str_replace("{clube.cidade.name}","-",$text);
            // Clube -> Cidade -> Estado
            $text = str_replace("{clube.cidade.estado.id}","-",$text);
            $text = str_replace("{clube.cidade.estado.name}","-",$text);
            $text = str_replace("{clube.cidade.estado.uf}","-",$text);
            // Clube -> Cidade -> Estado -> País
            $text = str_replace("{clube.cidade.estado.pais.id}","-",$text);
            $text = str_replace("{clube.cidade.estado.pais.name}","-",$text);
            $text = str_replace("{clube.cidade.estado.pais.iso}","-",$text);

        }
        // Torneio
        $text = str_replace("{torneio.id}",$inscricao->torneio->id,$text);
        $text = str_replace("{torneio.name}",$inscricao->torneio->name,$text);

        // Evento
        $text = str_replace("{evento.id}",$inscricao->torneio->evento->id,$text);
        $text = str_replace("{evento.name}",$inscricao->torneio->evento->name,$text);
        $text = str_replace("{evento.data.inicio}",$inscricao->torneio->evento->getDataInicio(),$text);
        $text = str_replace("{evento.data.fim}",$inscricao->torneio->evento->getDataFim(),$text);
        $text = str_replace("{evento.data.inscricoes}",$inscricao->torneio->evento->getDataFimInscricoesOnline(),$text);
        $text = str_replace("{evento.local}",$inscricao->torneio->evento->local,$text);
        $text = str_replace("{evento.link}",$inscricao->torneio->evento->local,$text);
        $text = str_replace("{evento.lichess.team}","https://lichess.org/team/".$inscricao->torneio->evento->lichess_team_id,$text);
        $text = str_replace("{evento.lichess.tournament}","https://lichess.org/swiss/".$inscricao->torneio->evento->lichess_tournament_id,$text);
        $text = str_replace("{evento.lichess.inscricao}",url("/inscricao/".$inscricao->uuid."/lichess"),$text);
        // Evento -> Cidade
        $text = str_replace("{evento.cidade.id}",$inscricao->torneio->evento->cidade->id,$text);
        $text = str_replace("{evento.cidade.name}",$inscricao->torneio->evento->cidade->name,$text);
        // Evento -> Cidade -> Estado
        $text = str_replace("{evento.cidade.estado.id}",$inscricao->torneio->evento->cidade->estado->id,$text);
        $text = str_replace("{evento.cidade.estado.name}",$inscricao->torneio->evento->cidade->estado->nome,$text);
        $text = str_replace("{evento.cidade.estado.uf}",$inscricao->torneio->evento->cidade->estado->abbr,$text);
        // Evento -> Cidade -> Estado -> País
        $text = str_replace("{evento.cidade.estado.pais.id}",$inscricao->torneio->evento->cidade->estado->pais->id,$text);
        $text = str_replace("{evento.cidade.estado.pais.name}",$inscricao->torneio->evento->cidade->estado->pais->nome,$text);
        $text = str_replace("{evento.cidade.estado.pais.iso}",$inscricao->torneio->evento->cidade->estado->pais->codigo_iso,$text);

        // Grupo de Evento
        $text = str_replace("{grupoevento.id}",$inscricao->torneio->evento->grupo_evento->id,$text);
        $text = str_replace("{grupoevento.name}",$inscricao->torneio->evento->grupo_evento->name,$text);
        $text = str_replace("{grupoevento.regulamento}",$inscricao->torneio->evento->grupo_evento->regulamento_link,$text);

        $text = $this->fillEnxadristaFields($text,$inscricao->enxadrista);

        return $text;
    }


    private function fillEnxadristaFields($text, $enxadrista){
        // Enxadrista
        $text = str_replace("{enxadrista.id}",$enxadrista->id,$text);
        $text = str_replace("{enxadrista.name}",$enxadrista->name,$text);
        $text = str_replace("{enxadrista.firstname}",$enxadrista->firstname,$text);
        $text = str_replace("{enxadrista.lastname}",$enxadrista->lastname,$text);
        $text = str_replace("{enxadrista.born}",$enxadrista->getBorn(),$text);

        $text = str_replace("{enxadrista.cbx.id}",$enxadrista->cbx_id,$text);
        $text = str_replace("{enxadrista.cbx.name}",$enxadrista->cbx_name,$text);

        $text = str_replace("{enxadrista.fide.id}",$enxadrista->fide_id,$text);
        $text = str_replace("{enxadrista.fide.name}",$enxadrista->fide_name,$text);

        $text = str_replace("{enxadrista.lbx.id}",$enxadrista->lbx_id,$text);
        $text = str_replace("{enxadrista.lbx.name}",$enxadrista->lbx_name,$text);

        $text = str_replace("{enxadrista.chess_com.username}",$enxadrista->chess_com_username,$text);
        $text = str_replace("{enxadrista.lichess.username}",$enxadrista->lichess_username,$text);

        // Enxadrista -> Sexo
        $text = str_replace("{enxadrista.sexo.id}", $enxadrista->sexo->id, $text);
        $text = str_replace("{enxadrista.sexo.name}", $enxadrista->sexo->name, $text);
        $text = str_replace("{enxadrista.sexo.abbr}", $enxadrista->sexo->abbr, $text);

        // Enxadrista -> País
        $text = str_replace("{enxadrista.pais.id}",$enxadrista->pais_nascimento->id,$text);
        $text = str_replace("{enxadrista.pais.name}",$enxadrista->pais_nascimento->nome,$text);
        $text = str_replace("{enxadrista.pais.iso}",$enxadrista->pais_nascimento->codigo_iso,$text);

        // Enxadrista -> Cidade
        $text = str_replace("{enxadrista.cidade.id}",$enxadrista->cidade->id,$text);
        $text = str_replace("{enxadrista.cidade.name}",$enxadrista->cidade->name,$text);
        // Enxadrista -> Cidade -> Estado
        $text = str_replace("{enxadrista.cidade.estado.id}",$enxadrista->cidade->estado->id,$text);
        $text = str_replace("{enxadrista.cidade.estado.name}",$enxadrista->cidade->estado->nome,$text);
        $text = str_replace("{enxadrista.cidade.estado.uf}",$enxadrista->cidade->estado->abbr,$text);
        // Enxadrista -> Cidade -> Estado -> País
        $text = str_replace("{enxadrista.cidade.estado.pais.id}",$enxadrista->cidade->estado->pais->id,$text);
        $text = str_replace("{enxadrista.cidade.estado.pais.name}",$enxadrista->cidade->estado->pais->nome,$text);
        $text = str_replace("{enxadrista.cidade.estado.pais.iso}",$enxadrista->cidade->estado->pais->codigo_iso,$text);

        if($enxadrista->clube){
            // Enxadrista -> Clube
            $text = str_replace("{enxadrista.clube.id}",$enxadrista->clube->id,$text);
            $text = str_replace("{enxadrista.clube.name}",$enxadrista->clube->name,$text);
            // Enxadrista -> Clube -> Cidade
            $text = str_replace("{enxadrista.clube.cidade.id}",$enxadrista->clube->cidade->id,$text);
            $text = str_replace("{enxadrista.clube.cidade.name}",$enxadrista->clube->cidade->name,$text);
            // Enxadrista -> Clube -> Cidade -> Estado
            $text = str_replace("{enxadrista.clube.cidade.estado.id}",$enxadrista->clube->cidade->estado->id,$text);
            $text = str_replace("{enxadrista.clube.cidade.estado.name}",$enxadrista->clube->cidade->estado->nome,$text);
            $text = str_replace("{enxadrista.clube.cidade.estado.uf}",$enxadrista->clube->cidade->estado->abbr,$text);
            // Enxadrista -> Clube -> Cidade -> Estado -> País
            $text = str_replace("{enxadrista.clube.cidade.estado.pais.id}",$enxadrista->clube->cidade->estado->pais->id,$text);
            $text = str_replace("{enxadrista.clube.cidade.estado.pais.name}",$enxadrista->clube->cidade->estado->pais->nome,$text);
            $text = str_replace("{enxadrista.clube.cidade.estado.pais.iso}",$enxadrista->clube->cidade->estado->pais->codigo_iso,$text);
        }else{
           // Enxadrista -> Clube
            $text = str_replace("{enxadrista.clube.id}", "-", $text);
            $text = str_replace("{enxadrista.clube.name}", "Sem Clube", $text);
            // Enxadrista -> Clube -> Cidade
            $text = str_replace("{enxadrista.clube.cidade.id}", "-", $text);
            $text = str_replace("{enxadrista.clube.cidade.name}", "-", $text);
            // Enxadrista -> Clube -> Cidade -> Estado
            $text = str_replace("{enxadrista.clube.cidade.estado.id}", "-", $text);
            $text = str_replace("{enxadrista.clube.cidade.estado.name}", "-", $text);
            $text = str_replace("{enxadrista.clube.cidade.estado.uf}", "-", $text);
            // Enxadrista -> Clube -> Cidade -> Estado -> País
            $text = str_replace("{enxadrista.clube.cidade.estado.pais.id}", "-", $text);
            $text = str_replace("{enxadrista.clube.cidade.estado.pais.name}", "-", $text);
            $text = str_replace("{enxadrista.clube.cidade.estado.pais.iso}", "-", $text);

        }

        return $text;
    }
}
