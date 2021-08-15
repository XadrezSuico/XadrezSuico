<?php
namespace App\Enum;

class EmailType
{
    private $types = array(
        1 => "Confirmação de Cadastro de Enxadrista",
        2 => "Confirmação de Inscrição",
        3 => "Confirmação de Inscrição no Lichess.org",
        4 => "Aviso de Necessidade de Inscrição no Torneio no Lichess.org",
    );

    public function list(){
        return $this->types;
    }

    public function get($id){
        return $this->types[($id)];
    }
}
