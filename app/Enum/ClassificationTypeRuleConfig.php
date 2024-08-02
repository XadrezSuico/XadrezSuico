<?php
namespace App\Enum;

class ClassificationTypeRuleConfig
{
    const REGISTRATIONS_MIN = "registration-min";
    const REGISTRATIONS_MAX = "registration-max";

    static $types = array(
        "registration-min" => array(
            "name" => "Inscritos: Quantidade Mínima para a Regra",
            "description" => "Indica a quantidade mínima de inscritos para que essa regra atue.",
            "type" => "integer"
        ),
        "registration-max" => array(
            "name" => "Inscritos: Quantidade Máxima para a Regra",
            "description" => "Indica a quantidade máxima de inscritos para que essa regra atue.",
            "type" => "integer"
        ),
    );

    public static function list(){
        return self::$types;
    }

    public static function get($id)
    {
        return self::$types[($id)];
    }
}
