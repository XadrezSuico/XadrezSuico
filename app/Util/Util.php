<?php
namespace App\Util;

class Util extends \App\Http\Util\Util
{
    public static function eGeracaoDeFamilia($nome){
        $nomes_geracao_familia = [
            'JULIOR',
            'FILHO',
            'NETO',
            'SOBRINHO',
            'SENIOR'
        ];
        if(in_array($nome, $nomes_geracao_familia)){
            return true;
        }
        return false;
    }
    public static function ePreposicao($nome){
        $preoposicoes = [
            'DE',
            'DOS',
            'DA',
            'DE',
            'DEL',
            'E'
        ];
        if(in_array($nome, $preoposicoes)){
            return true;
        }
        return false;
    }
}
