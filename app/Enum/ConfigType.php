<?php
namespace App\Enum;

class ConfigType
{
    const Integer = "integer";
    const Float = "float";
    const Decimal = "decimal";
    const Boolean = "boolean";
    const String = "string";
    const Date = "date";
    const DateTime = "datetime";

    private $types = array(
        "integer" => array("name"=>"Número Inteiro"),
        "float" => array("name"=>"Número Real"),
        "decimal" => array("name"=>"Número Real (Monetário)"),
        "boolean" => array("name"=>"Booleano"),
        "date" => array("name"=>"Data"),
        "datetime" => array("name" => "Data e Hora"),
    );

    public function list(){
        return $this->types;
    }

    public function get($id){
        return $this->types[($id)];
    }
}
