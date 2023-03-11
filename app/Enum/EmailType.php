<?php
namespace App\Enum;

class EmailType
{
    const CadastroEnxadrista = 1;
    const ConfirmacaoInscricao = 2;
    const ConfirmacaoInscricaoLichess = 3;
    const AvisoNecessidadeInscricaoLichess = 4;
    const InscricaoConfirmada = 5;
    const PagamentoConfirmado = 6;
    const InscricaoRecebidaPagamentoPendente = 7;
    const PagamentoConfirmadoInscricaoConfirmada = 8;
    const InscricaoConfirmadaComPagamentoAutomatico = 9;

    private $types = array(
        1 => array("name"=>"Confirmação de Cadastro de Enxadrista","is_general"=>1),
        2 => array("name"=>"Confirmação de Inscrição","is_general"=>0),
        3 => array("name"=>"Confirmação de Inscrição no Lichess.org","is_general"=>0),
        4 => array("name"=>"Aviso de Necessidade de Inscrição no Torneio no Lichess.org","is_general"=>0),
        5 => array("name"=>"Inscrição Confirmada","is_general"=>0),
        6 => array("name"=>"Pagamento Confirmado","is_general"=>0),
        7 => array("name"=>"Inscrição Recebida - Pagamento Pendente","is_general"=>0),
        8 => array("name"=>"Pagamento Confirmado - Inscrição Confirmada","is_general"=>0),
        9 => array("name"=>"Inscrição Confirmada Automaticamente Pelo Pagamento","is_general"=>0),
    );

    public function list(){
        return $this->types;
    }

    public function get($id){
        return $this->types[($id)];
    }
}
