<?php
namespace App\Enum;

class ClassificationTypeRuleConfig
{
    const REGISTRATIONS_MIN = "registration-min";
    const REGISTRATIONS_MAX =
    "registration-max";
    const DEFAULT = "default";

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
        "default" => array(
            "name" => "Regra Padrão",
            "description" => "Indica se essa regra é a padrão e será aplicada para todos os que não atenderem as outras regras.",
            "type" => "boolean"
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
