<?php

namespace App\Http\Controllers\API\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Pagina;

class PageController extends Controller
{
    public function get($uuid){
        if(Pagina::where([["uuid","=",$uuid]])->count() == 0){
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Página não encontrada.","httpcode"=>404],404);
        }

        $pagina = Pagina::where([["uuid","=",$uuid]])->first();
        if($pagina->evento){
            return response()->json(["ok"=>0,"error"=>1,"message"=>"A página está vinculada a um evento e com isso não pode ser exibida por aqui.","httpcode"=>400],400);
        }

        $page = array();
        $page["uuid"] = $pagina->uuid;
        $page["title"] = $pagina->title;
        $page["image"] = $pagina->imagem;
        $page["text"] = $pagina->texto;

        return response()->json(["ok"=>1,"error"=>0,"page"=>$page]);
    }
}
