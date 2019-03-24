<?php

namespace App\Http\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Util
{
  public static function theIP(){
    if(env("APP_TYPE") != "development"){
      return $_SERVER["HTTP_X_REAL_IP"];
    }else{
      return 0;
    }
  }

  public static function mes(){
    switch(date("n")){
      case 1: return "Janeiro"; break;
      case 2: return "Fevereiro"; break;
      case 3: return "Março"; break;
      case 4: return "Abril"; break;
      case 5: return "Maio"; break;
      case 6: return "Junho"; break;
      case 7: return "Julho"; break;
      case 8: return "Agosto"; break;
      case 9: return "Setembro"; break;
      case 10: return "Outubro"; break;
      case 11: return "Novembro"; break;
      case 12: return "Dezembro"; break;
    }
  }

  public static function numeros($str){
    return preg_replace("/[^0-9]/", "", $str);
  }
}
