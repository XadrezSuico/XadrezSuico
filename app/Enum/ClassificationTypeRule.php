<?php
namespace App\Enum;

class ClassificationTypeRule
{
    const POSITION = "position";
    const POSITION_ABSOLUTE = "position-absolute";
    const PRE_CLASSIFICATE = "pre-classificate";
    const PLACE_BY_QUANTITY = "place-by-quantity";

    static $types = array(
        "position" => array(
            "name" => "Posição",
            "description" => "Indica a quantidade de vagas considerando as primeiras posições da categoria."
        ),
        "position-absolute" => array(
            "name" => "Posição (Absoluta)",
            "description" => "Indica a quantidade de vagas por posição (sendo a posição absoluta)."
        ),
        "pre-classificate" => array(
            "name"=>"Pré-Classificado em Evento",
            "description" => "Indica qual evento deve-se considerar a pré-classificação, onde todos os participantes daquele evento tem o direito a vaga se jogarem o evento."
        ),
        "place-by-quantity" => array(
            "name"=>"Quantidade de Vagas por Participantes",
            "description" => "Indica a quantidade de vagas pela quantidade de participantes."
        ),
    );

    public static function list(){
        return self::$types;
    }

    public static function get($id){
        return self::$types[($id)];
    }
}
