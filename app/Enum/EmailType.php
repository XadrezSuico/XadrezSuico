<?php
namespace App\Enum;

class EmailType
{
    const CadastroEnxadrista = 1;
    const ConfirmacaoInscricao = 2;
    const ConfirmacaoInscricaoLichess = 3;
    const AvisoNecessidadeInscricaoLichess = 4;

    private $types = array(
        1 => array("name"=>"Confirmação de Cadastro de Enxadrista","is_general"=>1),
        2 => array("name"=>"Confirmação de Inscrição","is_general"=>0),
        3 => array("name"=>"Confirmação de Inscrição no Lichess.org","is_general"=>0),
        4 => array("name"=>"Aviso de Necessidade de Inscrição no Torneio no Lichess.org","is_general"=>0),
    );

    public function list(){
        return $this->types;
    }

    public function get($id){
        return $this->types[($id)];
    }
}
