<?php
namespace App\Enum;

class ConfigType
{
    const Integer = "integer";
    const Float = "float";
    const Decimal = "decimal";
    const Boolean = "boolean";
    const String = "string";

    private $types = array(
        "integer" => array("name"=>"Número Inteiro"),
        "float" => array("name"=>"Número Real"),
        "decimal" => array("name"=>"Número Real (Monetário)"),
        "boolean" => array("name"=>"Booleano"),
        "string" => array("name"=>"Texto Curto (até 255 caracteres)"),
    );

    public function list(){
        return $this->types;
    }

    public function get($id){
        return $this->types[($id)];
    }
}
