<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inscricao;
use App\Evento;
use App\Torneio;
use App\Cidade;
use App\Clube;
use App\Enxadrista;
use App\Categoria;

class InscricaoController extends Controller
{
    public function inscricao($id){
        $evento = Evento::find($id);
        if($evento){
            if($evento->inscricoes_encerradas()){
                return view("inscricao.encerradas",compact("evento"));
            }else{
                return view("inscricao.inscricao",compact("evento"));
            }
        }else{
            return view("inscricao.naoha");
        }
    }


    public function adicionarNovaInscricao(Request $request){
        if(
            !$request->has("regulamento_aceito")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Você deve aceitar o regulamento do evento!","registred"=>0]);
        }elseif(
            !$request->has("enxadrista_id") || !$request->has("categoria_id") || !$request->has("cidade_id") || !$request->has("evento_id")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!","registred"=>0]);
        }elseif(
            $request->input("enxadrista_id") == NULL || $request->input("enxadrista_id") == "" ||
            $request->input("categoria_id") == NULL || $request->input("categoria_id") == "" ||
            $request->input("cidade_id") == NULL || $request->input("cidade_id") == "" ||
            $request->input("evento_id") == NULL || $request->input("evento_id") == ""
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
        }

        $inscricao = new Inscricao;
        $torneio = NULL;
        $evento = Evento::find($request->input("evento_id"));

        if($evento->inscricoes_encerradas(true)){
            return response()->json(["ok"=>0,"error"=>1,"message" => "O prazo de inscrições antecipadas se esgotou. As inscrições poderão ser feitas no local do evento conforme regulamento."]);
        }

        foreach($evento->torneios->all() as $Torneio){
            foreach($Torneio->categorias->all() as $categoria){
                if($categoria->categoria_id == $request->input("categoria_id")){
                    $torneio = $Torneio;
                }
            }
        }
        if(!$torneio){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Ocorreu um erro inesperado de pesquisa de Torneio. Por favor, tente novamente mais tarde."]);
        }
        $temInscricao = $evento->torneios()->whereHas("inscricoes",function($q) use ($request){ 
            $q->where([["enxadrista_id","=",$request->input("enxadrista_id")]]);
        })->first();
        if(count($temInscricao) > 0){
            $inscricao = Inscricao::where([["enxadrista_id","=",$request->input("enxadrista_id")],["torneio_id","=",$temInscricao->id]])->first();
            return response()->json(["ok"=>0,"error"=>1,"message" => "Você já possui inscrição para este evento!<br/> Categoria: ".$inscricao->categoria->name."<br/> Caso queira efetuar alguma alteração, favor enviar via email para circuitoxadrezcascavel@gmail.com."]);
        }

        $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
        $categoria = Categoria::find($request->input("categoria_id"));
        if($categoria){
            if($categoria->idade_minima){
                if(!($categoria->idade_minima <= $enxadrista->howOld())){
                    return response()->json(["ok"=>0,"error"=>1,"message" => "Você não está apto a participar da categoria que você enviou! Motivo: Não possui idade mínima."]);
                }
            }
            if($categoria->idade_maxima){
                if(!($categoria->idade_maxima >= $enxadrista->howOld())){
                    return response()->json(["ok"=>0,"error"=>1,"message" => "Você não está apto a participar da categoria que você enviou! Motivo: Idade ultrapassa a máxima."]);
                }
            }
        }

        $inscricao->torneio_id = $torneio->id;
        $inscricao->enxadrista_id = $enxadrista->id;
        $inscricao->categoria_id = $categoria->id;
        $inscricao->cidade_id = $request->input("cidade_id");
        if($request->has("clube_id")){
            if($request->input("clube_id") > 0){
                $inscricao->clube_id = $request->input("clube_id");
            }
        }
        $inscricao->regulamento_aceito = true;
        $inscricao->save();
        if($inscricao->id > 0){
            return response()->json(["ok"=>1,"error"=>0]);
        }else{
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde."]);
        }
    }


    public function adicionarNovoEnxadrista(Request $request){
        if(
            !$request->has("name") || !$request->has("born") || !$request->has("cidade_id")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!","registred"=>0]);
        }elseif(
            $request->input("name") == NULL || $request->input("name") == "" ||
            $request->input("born") == NULL || $request->input("born") == "" ||
            $request->input("cidade_id") == NULL || $request->input("cidade_id") == ""
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!","registred"=>0]);
        }
        $enxadrista = new Enxadrista;
        if(!$enxadrista->setBorn($request->input("born"))){
            return response()->json(["ok"=>0,"error"=>1,"message" => "A data de nascimento é inválida.","registred"=>0]);
        }

        $temEnxadrista = Enxadrista::where([["name","=",mb_strtoupper($request->input("name"))],["born","=",$enxadrista->born]])->first();
        if(count($temEnxadrista) > 0){
            if($temEnxadrista->clube){
                return response()->json(["ok"=>0,"error"=>1,"message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!","registred"=>1,"enxadrista_id"=>$temEnxadrista->id,"enxadrista_name"=>$temEnxadrista->name." | ".$temEnxadrista->getBorn(),"cidade"=>["id"=>$temEnxadrista->cidade->id,"name"=>$temEnxadrista->cidade->name],"clube"=>["id"=>$temEnxadrista->clube->id,"name"=>$temEnxadrista->clube->name]]);
            }else{
                return response()->json([
                    "ok"=>0,
                    "error"=>1,
                    "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                    "registred"=>1,
                    "enxadrista_id"=>$temEnxadrista->id,
                    "enxadrista_name"=>$temEnxadrista->name." | ".$temEnxadrista->getBorn(),
                    "cidade"=>["id"=>$temEnxadrista->cidade->id,
                    "name"=>$temEnxadrista->cidade->name],
                    "clube"=>["id"=>0]
                ]);
            }
        }

        $enxadrista->name = mb_strtoupper($request->input("name"));
        $enxadrista->cidade_id = $request->input("cidade_id");
        if($request->has("clube_id")){
            if($request->input("clube_id") > 0){
                $enxadrista->clube_id = $request->input("clube_id");
            }
        }
        $enxadrista->save();
        if($enxadrista->id > 0){
            if($enxadrista->clube){
                return response()->json(["ok"=>1,"error"=>0,"enxadrista_id"=>$enxadrista->id,"cidade"=>["id"=>$enxadrista->cidade->id,"name"=>$enxadrista->cidade->name],"clube"=>["id"=>$enxadrista->clube->id,"name"=>$enxadrista->clube->name]]);
            }else{
                return response()->json(["ok"=>1,"error"=>0,"enxadrista_id"=>$enxadrista->id,"cidade"=>["id"=>$enxadrista->cidade->id,"name"=>$enxadrista->cidade->name],"clube"=>["id"=>0]]);
            }
        }else{
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.","registred"=>0]);
        }
    }

    
    public function adicionarNovaCidade(Request $request){
        if(
            !$request->has("name")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "O campo obrigatório não está preenchido. Por favor, verifique e envie novamente!","registred"=>0]);
        }elseif(
            $request->input("name") == NULL || $request->input("name") == ""
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "O campo obrigatório não está preenchido. Por favor, verifique e envie novamente!","registred"=>0]);
        }
        $cidade = new Cidade;

        $temCidade = Cidade::where([["name","=",mb_strtoupper($request->input("name"))]])->first();
        if(count($temCidade) > 0){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Esta cidade já está cadastrada! Selecionamos ela para você.","registred"=>1,"cidade"=>["id"=>$temCidade->id,"name"=>$temCidade->name]]);
        }

        $cidade->name = mb_strtoupper($request->input("name"));
        $cidade->save();
        if($cidade->id > 0){
            return response()->json(["ok"=>1,"error"=>0,"cidade"=>["id"=>$cidade->id,"name"=>$cidade->name]]);
        }else{
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.","registred"=>0]);
        }
    }
    
    public function adicionarNovoClube(Request $request){
        if(
            !$request->has("name") || !$request->has("cidade_id")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!","registred"=>0]);
        }elseif(
            $request->input("name") == NULL || $request->input("name") == "" ||
            $request->input("cidade_id") == NULL || $request->input("cidade_id") == ""
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!","registred"=>0]);
        }
        $clube = new Clube;

        $temClube = Clube::where([["name","=",mb_strtoupper($request->input("name"))],["cidade_id","=",$request->input("cidade_id")]])->first();
        if(count($temClube) > 0){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Este clube já está cadastrado! Selecionamos ele para você.","registred"=>1,"clube"=>["id"=>$temClube->id,"name"=>$temClube->name]]);
        }

        $clube->name = mb_strtoupper($request->input("name"));
        $clube->cidade_id = mb_strtoupper($request->input("cidade_id"));
        $clube->save();
        if($clube->id > 0){
            return response()->json(["ok"=>1,"error"=>0,"clube"=>["id"=>$clube->id,"name"=>$clube->name]]);
        }else{
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.","registred"=>0]);
        }
    }



    public function buscaEnxadrista(Request $request){
        $enxadristas = Enxadrista::where([
            ["name","like","%".$request->input("q")."%"],
        ])->orderBy("name","ASC")->get();
        $results = array();
        foreach($enxadristas as $enxadrista){
            $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name." | ".$enxadrista->getBorn());
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function buscaCategoria(Request $request){
        $evento = Evento::find($request->input("evento_id"));
        $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
        $categorias = $evento->categorias()->whereHas("categoria", function($QUERY) use ($request,$enxadrista){
            $QUERY->where([
                ["name","like","%".$request->input("q")."%"],
            ])
            ->where(function($query) use ($enxadrista){
                $query->where(function($q) use ($enxadrista){
                    $q->where([["idade_minima","<=",$enxadrista->howOld()]]);
                    $q->where([["idade_maxima",">=",$enxadrista->howOld()]]);
                })
                ->orWhere(function($q) use ($enxadrista){
                    $q->where([["idade_minima","<=",$enxadrista->howOld()]]);
                    $q->where([["idade_maxima","=",NULL]]);
                })
                ->orWhere(function($q) use ($enxadrista){
                    $q->where([["idade_minima","=",NULL]]);
                    $q->where([["idade_maxima",">=",$enxadrista->howOld()]]);
                })
                ->orWhere(function($q){
                    $q->where([["idade_minima","=",NULL]]);
                    $q->where([["idade_maxima","=",NULL]]);
                });
            });
        })
        ->get();
        $results = array();
        foreach($categorias as $categoria){
            $results[] = array("id" => $categoria->categoria->id, "text" => $categoria->categoria->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function buscaCidade(Request $request){
        $cidades = Cidade::where([
            ["name","like","%".$request->input("q")."%"],
          ])->get();
        $results = array();
        foreach($cidades as $cidade){
            $results[] = array("id" => $cidade->id, "text" => $cidade->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function buscaClube(Request $request){
        $clubes = Clube::where([
                ["name","like","%".$request->input("q")."%"],
            ])->orWhere(function($q) use ($request){
                $q->whereHas("cidade",function($Q) use ($request){
                    $Q->where([
                        ["name","like","%".$request->input("q")."%"],
                    ]);
                });
            })->get();
        $results = array(array("id" => -1, "text" => "Sem Clube"));
        foreach($clubes as $clube){
            $results[] = array("id" => $clube->id, "text" => $clube->cidade->name." - ".$clube->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

}
