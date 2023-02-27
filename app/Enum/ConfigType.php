<?php
namespace App\Enum;

class ConfigType
{
    const Integer = 1;
    const Float = 2;
    const Decimal = 3;
    const Boolean = 4;
    const String = 5;

    private $types = array(
        1 => array("name"=>"Número Inteiro"),
        2 => array("name"=>"Número Real"),
        3 => array("name"=>"Número Real (Monetário)"),
        4 => array("name"=>"Booleano"),
        5 => array("name"=>"Texto Curto (até 255 caracteres)"),
    );

    public function list(){
        return $this->types;
    }

    public function get($id){
        return $this->types[($id)];
    }
}
