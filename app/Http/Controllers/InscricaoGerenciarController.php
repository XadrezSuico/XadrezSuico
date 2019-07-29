<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Inscricao;
use App\Evento;
use App\Torneio;
use App\Cidade;
use App\Clube;
use App\Enxadrista;
use App\Categoria;
use App\Sexo;
use App\TipoRatingRegras;
use App\CampoPersonalizadoOpcaoInscricao;

class InscricaoGerenciarController extends Controller
{
    public function __construct(){
        $this->middleware("auth");
    }
	
	public function index($id,$torneio_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes->all();
		return view("evento.torneio.inscricao.index",compact("evento","torneio","inscricoes"));
	}
	
	public function edit($id,$torneio_id,$inscricao_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
		return view("evento.torneio.inscricao.edit",compact("evento","torneio","inscricao"));
	}
	
	public function edit_post($id,$torneio_id,$inscricao_id,Request $request){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
		if($inscricao){
            if($inscricao->categoria_id != $request->input("categoria_id")){
                $inscricao->categoria_id = $request->input("categoria_id");

                $torneio = null;

                foreach($inscricao->torneio->evento->torneios->all() as $Torneio){
                    foreach($Torneio->categorias->all() as $categoria){
                        if($categoria->categoria_id == $request->input("categoria_id")){
                            $torneio = $Torneio;
                        }
                    }
                }
                $inscricao->torneio_id = $torneio->id;

            }
            $inscricao->cidade_id = $request->input("cidade_id");
            
            if($request->has("desconsiderar_pontuacao_geral")){
                $inscricao->desconsiderar_pontuacao_geral = true;
            }else{
                $inscricao->desconsiderar_pontuacao_geral = false;
            }
            if($request->has("clube_id")){
                if($request->input("clube_id") > 0){
                    $inscricao->clube_id = $request->input("clube_id");
                }else{
                    $inscricao->clube_id = NULL;
                }
            }else{
                $inscricao->clube_id = NULL;
            }
            $inscricao->save();
            if($request->has("atualizar_cadastro")){
                $inscricao->enxadrista->cidade_id = $request->input("cidade_id");
                if($request->has("clube_id")){
                    if($request->input("clube_id") > 0){
                        $inscricao->enxadrista->clube_id = $request->input("clube_id");
                    }else{
                        $inscricao->enxadrista->clube_id = NULL;
                    }
                }else{
                    $inscricao->enxadrista->clube_id = NULL;
                }
                $inscricao->enxadrista->save();
            }
            return redirect("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$inscricao->id);


        }
	}
	
	public function unconfirm($id,$torneio_id,$inscricao_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
        if($inscricao->confirmado){
            $inscricao->confirmado = false;
            $inscricao->save();
        }
        return redirect("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes");
	}
	
	public function delete($id,$torneio_id,$inscricao_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
        if($inscricao->isDeletavel()){
            foreach($inscricao->opcoes->all() as $campo){
                $campo->delete();
            }
            $inscricao->delete();
        }
        return redirect("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes");
	}
	
	public function list_to_manager($id,$torneio_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->where([["confirmado","=",true]])->get();
        
        $texto = $this->generateTxt($inscricoes,$evento,$torneio);

        // file name that will be used in the download
        $fileName = "Exp_xadrezsuico_ev_".$id."_tor_".$torneio_id."_insc_conf___".date("Ymd-His")."___.TXT";

        // use headers in order to generate the download
        $headers = [
            'Content-type' => 'text/plain', 
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => sizeof($texto)
        ];

        // make a response, with the content, a 200 response code and the headers
        return response(utf8_decode($texto))->withHeaders([
                    'Content-Type' => 'text/plain; charset=utf-8',
                    'Cache-Control' => 'no-store, no-cache',
                    'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                ]);
    }

    
	public function list_to_manager_all($id,$torneio_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();
        
        $texto = $this->generateTxt($inscricoes,$evento,$torneio);

        // file name that will be used in the download
        $fileName = "Exp_xadrezsuico_ev_".$id."_tor_".$torneio_id."_tds_insc___".date("Ymd-His")."___.TXT";

        // use headers in order to generate the download
        $headers = [
        'Content-type' => 'text/plain', 
        'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
        'Content-Length' => sizeof($texto)
        ];

        // make a response, with the content, a 200 response code and the headers
        return response(utf8_decode($texto))->withHeaders([
                    'Content-Type' => 'text/plain; charset=utf-8',
                    'Cache-Control' => 'no-store, no-cache',
                    'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                ]);
    }

    private function generateTxt($inscricoes,$evento,$torneio){
        $texto = "No;Nome Completo;ID;";
        if($evento->tipo_rating){
            $texto .= "FIDE;";
        }else{
            if($evento->usa_fide){
                $texto .= "FIDE;";
            }
            if($evento->usa_cbx){
                $texto .= "Elonac;";
            }
        }
        $texto .= "DNasc;Cat;Gr;NoClube;Nome Clube;Sobrenome;Nome\r\n";
    
        $i = 1;
        
        $inscritos = array();
        foreach($inscricoes as $inscricao){
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController","cmp_obj"));

        foreach($inscritos as $inscricao){
            $texto .= $i++.";";
            $texto .= $inscricao->enxadrista->name.";";
            $texto .= $inscricao->enxadrista->id.";";

            if($evento->tipo_rating){
                $rating_regra = TipoRatingRegras::where([
                        ["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id],
                    ])
                    ->where(function($q1) use ($evento,$inscricao){
                        $q1->where([
                            ["idade_minima","<=",$inscricao->enxadrista->howOld()],
                            ["idade_maxima","=",NULL]
                        ]);
                        $q1->orWhere([
                            ["idade_minima","=",NULL],
                            ["idade_maxima",">=",$inscricao->enxadrista->howOld()]
                        ]);
                        $q1->orWhere([
                            ["idade_minima","<=",$inscricao->enxadrista->howOld()],
                            ["idade_maxima",">=",$inscricao->enxadrista->howOld()]
                        ]);
                    })
                    ->first();
                if($inscricao->enxadrista->ratings()->where([["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id]])->count() > 0){
                    $rating = $inscricao->enxadrista->ratings()->where([["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id]])->first();
                    
                    if($rating->valor > 0){
                        $texto .= $rating->valor.";";
                    }else{
                        $texto .= $rating_regra->inicial.";";
                    }
                    // $texto .= $rating_regra->k.";";
                }else{
                    $texto .= $rating_regra->inicial.";";
                    // $texto .= $rating_regra->k.";";
                }
            }else{
                if($evento->usa_fide){
                    $texto .= $inscricao->enxadrista->fide_rating.";";
                }
                if($evento->usa_cbx){
                    $texto .= $inscricao->enxadrista->cbx_rating.";";
                } 
            }

            $texto .= $inscricao->enxadrista->getBornToSM().";";
            $texto .= $inscricao->categoria->cat_code.";";
            $texto .= $inscricao->categoria->code.";";
            if($inscricao->clube){
                $texto .= $inscricao->clube->id.";";
                $texto .= $inscricao->cidade->name." - ".$inscricao->clube->name.";";
            }else{
                $texto .= ";";
                $texto .= $inscricao->cidade->name.";";
            }

            $nome_exp = explode(" ", $inscricao->enxadrista->name);
            $count_nome = count($nome_exp);
            $i = 1;
            foreach($nome_exp as $n_exp){
                if($i != $count_nome){
                    $texto .= $n_exp;
                }
                if($i < $count_nome - 1){
                    $texto .= " ";
                }else{
                    if($i == $count_nome){
                        $texto .= ";";
                    }
                }
                $i++;
            }
            $texto .= $nome_exp[$count_nome - 1]."\r\n";
        }
        return $texto;
    }

    static function cmp_obj($inscrito_a,$inscrito_b){
        $evento = $inscrito_a->torneio->evento;
        if($evento->tipo_rating){
            $r_a = $inscrito_a->enxadrista->ratingParaEvento($evento->id);
            $r_b = $inscrito_b->enxadrista->ratingParaEvento($evento->id);
            if($r_a == $r_b || !$r_a || !$r_b){
                return InscricaoGerenciarController::cmp_obj_alf($inscrito_a,$inscrito_b);
            }else{
                if($r_a > $r_b){
                    return -1;
                }
                return 1;
            }
        }else{
            if($evento->usa_fide){
                $r_a = $inscrito_a->enxadrista->fide_rating;
                $r_b = $inscrito_b->enxadrista->fide_rating;
                if(!($r_a == $r_b || !$r_a || !$r_b)){
                    if($r_a > $r_b){
                        return -1;
                    }
                    return 1;
                }
            }
            if($evento->usa_cbx){
                $r_a = $inscrito_a->enxadrista->cbx_rating;
                $r_b = $inscrito_b->enxadrista->cbx_rating;
                if(!($r_a == $r_b || !$r_a || !$r_b)){
                    if($r_a > $r_b){
                        return -1;
                    }
                    return 1;
                }
            }
            return InscricaoGerenciarController::cmp_obj_alf($inscrito_a,$inscrito_b);
        }
    }
    static function cmp_obj_alf($inscrito_a,$inscrito_b){
        return strnatcmp($inscrito_a->enxadrista->getName(),$inscrito_b->enxadrista->getName());
    }
    
    static function cmp_obj_club($inscrito_a,$inscrito_b){
        $evento = $inscrito_a->torneio->evento;
        if($inscrito_a->cidade_id == $inscrito_b->cidade_id){
            if($inscrito_a->clube_id == $inscrito_b->clube_id){
                return InscricaoGerenciarController::cmp_obj_alf($inscrito_a,$inscrito_b);
            }else{
                if($inscrito_a->clube_id == NULL || $inscrito_b->clube_id == NULL){
                    return InscricaoGerenciarController::cmp_obj_alf($inscrito_a,$inscrito_b);
                }
                return strnatcmp($inscrito_a->clube->getName(),$inscrito_b->clube->getName());
            }
        }else{
            return strnatcmp($inscrito_a->cidade->getName(),$inscrito_b->cidade->getName());
        }     
    }

	public function report_list_subscriptions($id,$torneio_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();

        $inscritos = array();
        foreach($inscricoes as $inscricao){
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController","cmp_obj"));
        
        return view("evento.torneio.inscricao.relatorio.inscritos",compact("evento","torneio","inscritos"));
    }

	public function report_list_subscriptions_alf($id,$torneio_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();

        $inscritos = array();
        foreach($inscricoes as $inscricao){
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController","cmp_obj_alf"));
        
        return view("evento.torneio.inscricao.relatorio.inscritos",compact("evento","torneio","inscritos"));
    }

	public function report_list_subscriptions_cidade_alf($id,$torneio_id){
        $evento = Evento::find($id);
        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();

        $inscritos = array();
        foreach($inscricoes as $inscricao){
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController","cmp_obj_club"));
        
        return view("evento.torneio.inscricao.relatorio.inscritos",compact("evento","torneio","inscritos"));
    }










    public function inscricao($id){
        $evento = Evento::find($id);
        $sexos = Sexo::all();
        if($evento){
            return view("inscricao.gerenciar.inscricao",compact("evento","sexos"));
        }else{
            return view("inscricao.gerenciar.naoha");
        }
    }


    public function adicionarNovaInscricao(Request $request){
        if(
            !$request->has("enxadrista_id") || 
            !$request->has("categoria_id") || 
            !$request->has("cidade_id") || 
            !$request->has("evento_id")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!","registred"=>0]);
        }elseif(
            $request->input("enxadrista_id") == NULL || $request->input("enxadrista_id") == "" ||
            $request->input("categoria_id") == NULL || $request->input("categoria_id") == "" ||
            $request->input("cidade_id") == NULL || $request->input("cidade_id") == "" ||
            $request->input("evento_id") == NULL || $request->input("evento_id") == ""
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!","registred"=>0]);
        }

        $inscricao = new Inscricao;
        $torneio = NULL;
        $evento = Evento::find($request->input("evento_id"));

        foreach($evento->campos()->whereHas("campo",function($q1){$q1->where([["is_required","=",true]]);})->get() as $campo){
            if(
                !$request->has("campo_personalizado_".$campo->campo->id)
            ){
                return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!","registred"=>0]);
            }elseif(
                $request->input("campo_personalizado_".$campo->campo->id) == NULL || $request->input("campo_personalizado_".$campo->campo->id) == ""
            ){
                return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!","registred"=>0]);
            }
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
        if($request->has("confirmado")){
            $inscricao->confirmado = true;
        }
        $inscricao->regulamento_aceito = true;
        $inscricao->save();

        
        foreach($evento->campos->all() as $campo){
            $opcao_inscricao = new CampoPersonalizadoOpcaoInscricao;
            $opcao_inscricao->inscricao_id = $inscricao->id;
            $opcao_inscricao->opcaos_id = $request->input("campo_personalizado_".$campo->campo->id);
            $opcao_inscricao->campo_personalizados_id = $campo->campo->id;
            $opcao_inscricao->save();
        }

        if($request->has("atualizar_cadastro")){
            $enxadrista->cidade_id = $inscricao->cidade_id;
            if($request->has("clube_id")){
                if($request->input("clube_id") > 0){
                    $enxadrista->clube_id = $request->input("clube_id");
                }else{
                    $enxadrista->clube_id = NULL;
                }
            }else{
                $enxadrista->clube_id = NULL;
            }
            $enxadrista->save();
            
        
            if($inscricao->id > 0){
                if($inscricao->confirmado){
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>1,"confirmed"=>1]);
                }else{
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>1,"confirmed"=>0]);
                }
            }else{
                return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.","updated"=>1]);
            }
        }else{
            if($inscricao->id > 0){
                if($inscricao->confirmado){
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>0,"confirmed"=>1]);
                }else{
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>0,"confirmed"=>0]);
                }
            }else{
                return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.","updated"=>0]);
            }
        }
    }


    public function adicionarNovoEnxadrista(Request $request){
        if(
            !$request->has("name") || 
            !$request->has("born") || 
            !$request->has("sexos_id") || 
            !$request->has("cidade_id")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!","registred"=>0]);
        }elseif(
            $request->input("name") == NULL || $request->input("name") == "" ||
            $request->input("born") == NULL || $request->input("born") == "" ||
            $request->input("sexos_id") == NULL || $request->input("sexos_id") == "" ||
            $request->input("cidade_id") == NULL || $request->input("cidade_id") == ""
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!","registred"=>0]);
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
        $enxadrista->sexos_id = $request->input("sexos_id");
        if($request->has("cbx_id")){
            if($request->input("cbx_id") > 0){
                $enxadrista->cbx_id = $request->input("cbx_id");
            }
        }
        if($request->has("fide_id")){
            if($request->input("fide_id") > 0){
                $enxadrista->fide_id = $request->input("fide_id");
            }
        }
        if($request->has("lbx_id")){
            if($request->input("lbx_id") > 0){
                $enxadrista->lbx_id = $request->input("lbx_id");
            }
        }
        if($request->has("email")){
            if($request->input("email") != ""){
                $enxadrista->email = $request->input("email");
            }
        }
        if($request->has("celular")){
            if($request->input("celular") != ""){
                $enxadrista->celular = $request->input("celular");
            }
        }
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
            if($enxadrista->estaInscrito($request->input("evento_id"))){
                $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name." | ".$enxadrista->getBorn()." - Já Está Inscrito neste Evento");
            }else 
                $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name." | ".$enxadrista->getBorn());
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function getCidadeClube($id,$enxadrista_id){
        $enxadrista = Enxadrista::find($enxadrista_id);
        if($enxadrista){
            if($enxadrista->clube){
                return response()->json(["ok"=>1,"error"=>0,"cidade"=>["id"=>$enxadrista->cidade->id,"name"=>$enxadrista->cidade->name],"clube"=>["id"=>$enxadrista->clube->id,"name"=>$enxadrista->clube->name]]);
            }else{
                return response()->json(["ok"=>1,"error"=>0,"cidade"=>["id"=>$enxadrista->cidade->id,"name"=>$enxadrista->cidade->name],"clube"=>["id"=>0]]);
            }
        }else{
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Não há enxadrista com esse código!"]);
        }
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
            })
            ->where(function($q2) use ($enxadrista){
                $q2->where(function($q3) use ($enxadrista){
                    if($enxadrista->sexos_id){
                        $q3->where(function($q4) use ($enxadrista){
                            $q4->whereHas("sexos",function($q5) use ($enxadrista){
                                $q5->where([["sexos_id","=",$enxadrista->sexos_id]]);
                            });
                        });
                        $q3->orWhere(function($q4){
                            $q4->doesntHave("sexos");
                        });
                    }else{
                        $q3->whereDoesntHave("sexos");
                    }
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











    

    public function confirmacao($id){
        $evento = Evento::find($id);
        if($evento){
            return view("inscricao.gerenciar.confirmar",compact("evento"));
        }else{
            return view("inscricao.gerenciar.naoha");
        }
    }
    public function buscaEnxadristaParaConfirmacao($id,Request $request){
        $inscricoes = Inscricao::where(function($q1) use ($id,$request){
            $q1->whereHas("enxadrista",function($q2) use ($request){
                $q2->where([
                    ["name","like","%".$request->input("q")."%"],
                ])->orderBy("name","ASC");
            });
            $q1->whereHas("torneio",function($q2) use ($id,$request){
                $q2->where([
                    ["evento_id","=",$id]
                ]);
            });
            $q1->where([
                ["confirmado","=",false]
            ]);
        })
        ->orderBy("id","ASC")
        ->get();
        $results = array();
        foreach($inscricoes as $inscricao){
            $results[] = array("id" => $inscricao->id, "text" => "[#".$inscricao->id."] ".$inscricao->enxadrista->name." | ".$inscricao->enxadrista->getBorn());
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function getInscricaoDados($id,$inscricao_id){
        $inscricao = Inscricao::find($inscricao_id);
        if($inscricao){
            if($inscricao->clube){
                return response()->json(["ok"=>1,"error"=>0,"enxadrista"=>["id" => $inscricao->enxadrista->id], "cidade"=>["id"=>$inscricao->cidade->id,"name"=>$inscricao->cidade->name],"categoria"=>["id"=>$inscricao->categoria->id,"name"=>$inscricao->categoria->name],"clube"=>["id"=>$inscricao->clube->id,"name"=>$inscricao->clube->name]]);
            }else{
                return response()->json(["ok"=>1,"error"=>0,"enxadrista"=>["id" => $inscricao->enxadrista->id], "cidade"=>["id"=>$inscricao->cidade->id,"name"=>$inscricao->cidade->name],"categoria"=>["id"=>$inscricao->categoria->id,"name"=>$inscricao->categoria->name],"clube"=>["id"=>0]]);
            }
        }else{
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Não há enxadrista com esse código!"]);
        }
    }

    public function confirmarInscricao(Request $request){
        if(
            !$request->has("inscricao_id") || !$request->has("categoria_id") || !$request->has("cidade_id") || !$request->has("evento_id")
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!","registred"=>0]);
        }elseif(
            $request->input("inscricao_id") == NULL || $request->input("inscricao_id") == "" ||
            $request->input("categoria_id") == NULL || $request->input("categoria_id") == "" ||
            $request->input("cidade_id") == NULL || $request->input("cidade_id") == "" ||
            $request->input("evento_id") == NULL || $request->input("evento_id") == ""
        ){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
        }

        $inscricao = Inscricao::find($request->input("inscricao_id"));
        if(!$inscricao){
            return response()->json(["ok"=>0,"error"=>1,"message" => "Não existe um inscrição com o código informado!"]);
        }
        if($inscricao->confirmado){
            return response()->json(["ok"=>0,"error"=>1,"message" => "A inscrição já está confirmada!"]);
        }
        $torneio = NULL;
        if($request->input("categoria_id") != $inscricao->categoria_id){
            $evento = Evento::find($request->input("evento_id"));

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
            $inscricao->categoria_id = $categoria->id;
            $inscricao->torneio_id = $torneio->id;
        }

        if($inscricao->cidade_id != $request->input("cidade_id")) $inscricao->cidade_id = $request->input("cidade_id");
        if($inscricao->clube_id != $request->input("clube_id"))
            if($request->has("clube_id")){
                if($request->input("clube_id") > 0){
                    $inscricao->clube_id = $request->input("clube_id");
                }
            }
        $inscricao->confirmado = true;
        $inscricao->regulamento_aceito = true;
        $inscricao->save();

        if($request->has("atualizar_cadastro")){
            $enxadrista = Enxadrista::find($inscricao->enxadrista_id);
            $enxadrista->cidade_id = $inscricao->cidade_id;
            if($request->has("clube_id")){
                if($request->input("clube_id") > 0){
                    $enxadrista->clube_id = $request->input("clube_id");
                }else{
                    $enxadrista->clube_id = NULL;
                }
            }else{
                $enxadrista->clube_id = NULL;
            }
            $enxadrista->save();
            
        
            if($inscricao->id > 0){
                if($inscricao->confirmado){
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>1,"confirmed"=>1]);
                }else{
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>1,"confirmed"=>0]);
                }
            }else{
                return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.","updated"=>1]);
            }
        }else{
            if($inscricao->id > 0){
                if($inscricao->confirmado){
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>0,"confirmed"=>1]);
                }else{
                    return response()->json(["ok"=>1,"error"=>0,"updated"=>0,"confirmed"=>0]);
                }
            }else{
                return response()->json(["ok"=>0,"error"=>1,"message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.","updated"=>0]);
            }
        }
    }
}
