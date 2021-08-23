<?php

namespace App\Http\Controllers;

use App\CampoPersonalizadoOpcaoInscricao;
use App\Categoria;
use App\Cidade;
use App\Clube;
use App\CriterioDesempate;
use App\Enxadrista;
use App\Evento;
use App\Inscricao;
use App\InscricaoCriterioDesempate;
use App\Sexo;
use App\Torneio;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use Twilio\Rest\Client;


class InscricaoGerenciarController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function index($id, $torneio_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes->all();
        return view("evento.torneio.inscricao.index", compact("evento", "torneio", "inscricoes"));
    }

    public function edit($id, $torneio_id, $inscricao_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
        if ($evento->e_resultados_manuais) {
            $criterios = $torneio->getCriteriosTotal();
        } else {
            $criterios = $torneio->getCriteriosManuais();
        }
        return view("evento.torneio.inscricao.edit", compact("evento", "torneio", "inscricao", "criterios"));
    }

    public function edit_post($id, $torneio_id, $inscricao_id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }
        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
        if ($inscricao) {
            if ($inscricao->categoria_id != $request->input("categoria_id")) {
                $inscricao->categoria_id = $request->input("categoria_id");

                $torneio = null;

                foreach ($inscricao->torneio->evento->torneios->all() as $Torneio) {
                    foreach ($Torneio->categorias->all() as $categoria) {
                        if ($categoria->categoria_id == $request->input("categoria_id")) {
                            $torneio = $Torneio;
                        }
                    }
                }
                $inscricao->torneio_id = $torneio->id;

            }
            $inscricao->cidade_id = $request->input("cidade_id");

            if ($request->has("desconsiderar_pontuacao_geral")) {
                $inscricao->desconsiderar_pontuacao_geral = true;
            } else {
                $inscricao->desconsiderar_pontuacao_geral = false;
            }
            if ($request->has("desconsiderar_classificado")) {
                $inscricao->desconsiderar_classificado = true;
            } else {
                $inscricao->desconsiderar_classificado = false;
            }
            if ($request->has("clube_id")) {
                if ($request->input("clube_id") > 0) {
                    $inscricao->clube_id = $request->input("clube_id");
                } else {
                    $inscricao->clube_id = null;
                }
            } else {
                $inscricao->clube_id = null;
            }
            $inscricao->save();
            if ($request->has("atualizar_cadastro")) {
                $inscricao->enxadrista->cidade_id = $request->input("cidade_id");
                if ($request->has("clube_id")) {
                    if ($request->input("clube_id") > 0) {
                        $inscricao->enxadrista->clube_id = $request->input("clube_id");
                    } else {
                        $inscricao->enxadrista->clube_id = null;
                    }
                } else {
                    $inscricao->enxadrista->clube_id = null;
                }
                $inscricao->enxadrista->save();
            }

            foreach ($evento->campos() as $campo) {
                if ($request->has("campo_personalizado_" . $campo->id)) {
                    if ($request->input("campo_personalizado_" . $campo->id) != "") {
                        $opcao_inscricao = CampoPersonalizadoOpcaoInscricao::where([["inscricao_id", "=", $inscricao->id], ["campo_personalizados_id", "=", $campo->id]])->first();
                        if (!$opcao_inscricao) {
                            $opcao_inscricao = new CampoPersonalizadoOpcaoInscricao;
                            $opcao_inscricao->inscricao_id = $inscricao->id;
                            $opcao_inscricao->campo_personalizados_id = $campo->id;
                        }
                        $opcao_inscricao->opcaos_id = $request->input("campo_personalizado_" . $campo->id);
                        $opcao_inscricao->save();
                    }
                }
            }

            if ($evento->e_resultados_manuais && $inscricao->confirmado) {
                if ($request->has("posicao")) {
                    $inscricao->posicao = $request->input("posicao");
                } else {
                    $inscricao->posicao = null;
                }

                if ($request->has("pontos")) {
                    $inscricao->pontos = $request->input("pontos");
                } else {
                    $inscricao->pontos = null;
                }

                if ($request->has("pontos_geral")) {
                    $inscricao->pontos_geral = $request->input("pontos_geral");
                } else {
                    $inscricao->pontos_geral = null;
                }

                if ($request->has("posicao_geral")) {
                    $inscricao->posicao_geral = $request->input("posicao_geral");
                } else {
                    $inscricao->posicao_geral = null;
                }
                $inscricao->save();

                foreach ($torneio->getCriteriosTotal() as $criterio) {
                    $criterio_salvar = $criterio->criterio->valor_criterio($inscricao->id);
                    if ($request->has("criterio_" . $criterio->criterio->id)) {
                        if (is_numeric($request->input("criterio_" . $criterio->criterio->id))) {
                            if (!$criterio_salvar) {
                                $criterio_salvar = new InscricaoCriterioDesempate;
                                $criterio_salvar->criterio_desempate_id = $criterio->criterio->id;
                                $criterio_salvar->inscricao_id = $inscricao->id;
                            }
                            $criterio_salvar->valor = $request->input("criterio_" . $criterio->criterio->id);
                            $criterio_salvar->save();
                        } else {
                            if ($criterio_salvar) {
                                $criterio_salvar->delete();
                            }

                        }
                    } else {
                        if ($criterio_salvar) {
                            $criterio_salvar->delete();
                        }

                    }
                }

            } else {
                foreach ($torneio->getCriteriosManuais() as $criterio) {
                    $criterio_salvar = $criterio->criterio->valor_criterio($inscricao->id);
                    if ($request->has("criterio_" . $criterio->criterio->id)) {
                        if (is_numeric($request->input("criterio_" . $criterio->criterio->id))) {
                            if (!$criterio_salvar) {
                                $criterio_salvar = new InscricaoCriterioDesempate;
                                $criterio_salvar->criterio_desempate_id = $criterio->criterio->id;
                                $criterio_salvar->inscricao_id = $inscricao->id;
                            }
                            $criterio_salvar->valor = $request->input("criterio_" . $criterio->criterio->id);
                            $criterio_salvar->save();
                        } else {
                            if ($criterio_salvar) {
                                $criterio_salvar->delete();
                            }

                        }
                    } else {
                        if ($criterio_salvar) {
                            $criterio_salvar->delete();
                        }

                    }
                }
            }

            return redirect("/evento/" . $evento->id . "/torneios/" . $torneio->id . "/inscricoes/edit/" . $inscricao->id);

        }
    }

    public function unconfirm($id, $torneio_id, $inscricao_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
        if ($inscricao->confirmado) {
            $inscricao->confirmado = false;
            $inscricao->save();
        }
        return redirect("/evento/" . $evento->id . "/torneios/" . $torneio->id . "/inscricoes");
    }

    public function delete($id, $torneio_id, $inscricao_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);
        if ($inscricao->isDeletavel()) {
            foreach ($inscricao->opcoes->all() as $campo) {
                $campo->delete();
            }
            $inscricao->delete();
        }
        return redirect("/evento/" . $evento->id . "/torneios/" . $torneio->id . "/inscricoes");
    }

    public function list_to_manager($id, $torneio_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->where([["confirmado", "=", true]])->get();

        $texto = $this->generateTxt($inscricoes, $evento, $torneio);

        // file name that will be used in the download
        $fileName = "Exp_xadrezsuico_ev_" . $id . "_tor_" . $torneio_id . "_insc_conf___" . date("Ymd-His") . "___.TXT";

        // use headers in order to generate the download
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => sizeof($texto),
        ];

        // make a response, with the content, a 200 response code and the headers
        return response(utf8_decode($texto))->withHeaders([
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function list_to_manager_all($id, $torneio_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();

        $texto = $this->generateTxt($inscricoes, $evento, $torneio);

        // file name that will be used in the download
        $fileName = "Exp_xadrezsuico_ev_" . $id . "_tor_" . $torneio_id . "_tds_insc___" . date("Ymd-His") . "___.TXT";

        // use headers in order to generate the download
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => sizeof($texto),
        ];

        // make a response, with the content, a 200 response code and the headers
        return response(utf8_decode($texto))->withHeaders([
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function generateTxt($inscricoes, $evento, $torneio)
    {
        switch($evento->exportacao_sm_modelo){
            case 1:
                // FIDE
                return $this->generateTxt_1($inscricoes, $evento, $torneio);
            case 2:
                // LBX
                return $this->generateTxt_2($inscricoes, $evento, $torneio);
            default:
                // Padrão XadrezSuíço
                return $this->generateTxt_0($inscricoes, $evento, $torneio);
        }
    }

    private function generateTxt_0($inscricoes, $evento, $torneio){
        $texto = "No;Nome Completo;ID;FED;";
        if ($evento->tipo_rating) {
            $texto .= "FIDE;";
        } else {
            if ($evento->usa_fide) {
                $texto .= "FIDE;";
                $texto .= "id FIDE;";
            } elseif ($evento->usa_lbx) {
                $texto .= "FIDE;";
                $texto .= "id FIDE;";
            }
            if ($evento->usa_cbx) {
                $texto .= "Elonac;";
            }
        }
        $texto .= "DNasc;Cat;Gr;NoClube;Nome Clube;Sobrenome;Nome;Fonte\r\n";

        $i = 1;

        $inscritos = array();
        foreach ($inscricoes as $inscricao) {
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController", "cmp_obj"));

        foreach ($inscritos as $inscricao) {
            $texto .= $i++ . ";";
            $texto .= $inscricao->enxadrista->name . ";";
            if($evento->calcula_cbx){
                $texto .= $inscricao->enxadrista->cbx_id . ";";
            }else{
                $texto .= $inscricao->enxadrista->id . ";";
            }
            $texto .= $inscricao->enxadrista->pais_nascimento->codigo_iso.";";

            if ($evento->tipo_rating) {
                if ($inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id)) {
                    $texto .= $inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id) . ";";
                } else {
                    $texto .= $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $evento) . ";";
                }
            } else {
                if ($evento->usa_fide) {
                    $texto .= $inscricao->enxadrista->showRating(0, $evento->tipo_modalidade) . ";";
                    $texto .= $inscricao->enxadrista->fide_id . ";";
                } elseif ($evento->usa_lbx) {
                    $texto .= $inscricao->enxadrista->showRating(2, $evento->tipo_modalidade) . ";";
                    $texto .= $inscricao->enxadrista->lbx_id . ";";
                }
                if ($evento->usa_cbx) {
                    $texto .= $inscricao->enxadrista->showRating(1, $evento->tipo_modalidade) . ";";
                }
            }

            $texto .= $inscricao->enxadrista->getBornToSM() . ";";
            $texto .= $inscricao->categoria->cat_code . ";";
            $texto .= $inscricao->categoria->code . ";";
            if ($inscricao->clube) {
                $texto .= $inscricao->cidade->ibge_id . ";";
                $texto .= $inscricao->cidade->name . " - " . $inscricao->clube->name . ";";
            } else {
                $texto .= $inscricao->cidade->ibge_id . ";";
                $texto .= $inscricao->cidade->name . ";";
            }

            $texto .= $inscricao->enxadrista->lastname . ";";
            $texto .= $inscricao->enxadrista->firstname . ";";
            $texto .= $inscricao->enxadrista->id . "\r\n";
        }
        return $texto;
    }

    private function generateTxt_1($inscricoes, $evento, $torneio){
        $texto = "No;Nome Completo;ID;FED;FIDE;id FIDE;";
        if ($evento->usa_cbx) {
            $texto .= "Elonac;";
        }
        $texto .= "Sexo;DNasc;Cat;Gr;NoClube;Nome Clube;Sobrenome;Nome;Fonte\r\n";

        $i = 1;

        $inscritos = array();
        foreach ($inscricoes as $inscricao) {
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController", "cmp_obj"));

        foreach ($inscritos as $inscricao) {
            $explode_fide_name = explode(",",$inscricao->enxadrista->fide_name);

            $texto .= $i++ . ";";
            $texto .= $inscricao->enxadrista->name . ";";
            $texto .= $inscricao->enxadrista->cbx_id . ";";
            $texto .= $inscricao->enxadrista->pais_nascimento->codigo_iso.";";

            if ($evento->tipo_rating) {
                if ($inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id)) {
                    $texto .= $inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id) . ";";
                } else {
                    $texto .= $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $evento) . ";";
                }
            } else {
                if ($evento->usa_fide) {
                    $texto .= $inscricao->enxadrista->showRating(0, $evento->tipo_modalidade) . ";";
                    $texto .= $inscricao->enxadrista->fide_id . ";";
                } elseif ($evento->usa_lbx) {
                    $texto .= $inscricao->enxadrista->showRating(2, $evento->tipo_modalidade) . ";";
                    $texto .= $inscricao->enxadrista->lbx_id . ";";
                }
                if ($evento->usa_cbx) {
                    $texto .= $inscricao->enxadrista->showRating(1, $evento->tipo_modalidade) . ";";
                }
            }
            if($inscricao->enxadrista->sexo->abbr == "F"){
                $texto .= "f;";
            }else{
                $texto .= ";";
            }
            $texto .= $inscricao->enxadrista->getBornToSM() . ";";
            $texto .= $inscricao->categoria->cat_code . ";";
            $texto .= $inscricao->categoria->code . ";";
            if ($inscricao->clube) {
                $texto .= $inscricao->clube->id . ";";
                $texto .= $inscricao->cidade->name . " - " . $inscricao->clube->name . ";";
            } else {
                $texto .= ";";
                $texto .= $inscricao->cidade->name . ";";
            }

            $texto .= ((count($explode_fide_name) > 1) ? $explode_fide_name[0] : $inscricao->enxadrista->lastname) . ";";
            $texto .= ((count($explode_fide_name) > 1) ? $explode_fide_name[1] : $inscricao->enxadrista->firstname) . ";";
            $texto .= $inscricao->enxadrista->id . "\r\n";
        }
        return $texto;
    }

    private function generateTxt_2($inscricoes, $evento, $torneio){
        $texto = "No;Nome Completo;ID;FED;";
        if ($evento->tipo_rating) {
            $texto .= "FIDE;";
        } else {
            if ($evento->usa_fide) {
                $texto .= "FIDE;";
                $texto .= "id FIDE;";
            } elseif ($evento->usa_lbx) {
                $texto .= "FIDE;";
                $texto .= "id FIDE;";
            }
            if ($evento->usa_cbx) {
                $texto .= "Elonac;";
            }
        }
        $texto .= "DNasc;Cat;Gr;NoClube;Nome Clube;Sobrenome;Nome;Fonte\r\n";

        $i = 1;

        $inscritos = array();
        foreach ($inscricoes as $inscricao) {
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController", "cmp_obj"));

        foreach ($inscritos as $inscricao) {
            $texto .= $i++ . ";";
            $texto .= $inscricao->enxadrista->name . ";";
            if($evento->calcula_cbx){
                $texto .= $inscricao->enxadrista->cbx_id . ";";
            }else{
                $texto .= $inscricao->enxadrista->id . ";";
            }
            $texto .= $inscricao->enxadrista->pais_nascimento->codigo_iso.";";

            if ($evento->tipo_rating) {
                if ($inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id)) {
                    $texto .= $inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id) . ";";
                } else {
                    $texto .= $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(), $evento) . ";";
                }
            } else {
                if ($evento->usa_fide) {
                    $texto .= $inscricao->enxadrista->showRating(0, $evento->tipo_modalidade) . ";";
                    $texto .= $inscricao->enxadrista->fide_id . ";";
                } elseif ($evento->usa_lbx) {
                    $texto .= $inscricao->enxadrista->showRating(2, $evento->tipo_modalidade) . ";";
                    $texto .= $inscricao->enxadrista->lbx_id . ";";
                }
                if ($evento->usa_cbx) {
                    $texto .= $inscricao->enxadrista->showRating(1, $evento->tipo_modalidade) . ";";
                }
            }

            $texto .= $inscricao->enxadrista->getBornToSM() . ";";
            $texto .= $inscricao->categoria->cat_code . ";";
            $texto .= $inscricao->categoria->code . ";";
            if ($inscricao->clube) {
                $texto .= $inscricao->cidade->ibge_id . ";";
                $texto .= $inscricao->cidade->name . " - " . $inscricao->clube->name . ";";
            } else {
                $texto .= $inscricao->cidade->ibge_id . ";";
                $texto .= $inscricao->cidade->name . ";";
            }

            $texto .= $inscricao->enxadrista->lastname . ";";
            $texto .= $inscricao->enxadrista->firstname . ";";
            $texto .= $inscricao->enxadrista->id . "\r\n";
        }
        return $texto;
    }

    public static function cmp_obj($inscrito_a, $inscrito_b)
    {
        $evento = $inscrito_a->torneio->evento;
        if ($evento->tipo_rating) {
            $r_a = $inscrito_a->enxadrista->ratingParaEvento($evento->id);
            $r_b = $inscrito_b->enxadrista->ratingParaEvento($evento->id);
            if ($r_a == $r_b || !$r_a || !$r_b) {
                return InscricaoGerenciarController::cmp_obj_alf($inscrito_a, $inscrito_b);
            } else {
                if ($r_a > $r_b) {
                    return -1;
                }
                return 1;
            }
        } else {
            if ($evento->usa_fide) {
                $r_a = $inscrito_a->enxadrista->fide_rating;
                $r_b = $inscrito_b->enxadrista->fide_rating;
                if (!($r_a == $r_b || !$r_a || !$r_b)) {
                    if ($r_a > $r_b) {
                        return -1;
                    }
                    return 1;
                }
            }
            if ($evento->usa_cbx) {
                $r_a = $inscrito_a->enxadrista->cbx_rating;
                $r_b = $inscrito_b->enxadrista->cbx_rating;
                if (!($r_a == $r_b || !$r_a || !$r_b)) {
                    if ($r_a > $r_b) {
                        return -1;
                    }
                    return 1;
                }
            }
            return InscricaoGerenciarController::cmp_obj_alf($inscrito_a, $inscrito_b);
        }
    }
    public static function cmp_obj_alf($inscrito_a, $inscrito_b)
    {
        return strnatcmp($inscrito_a->enxadrista->getName(), $inscrito_b->enxadrista->getName());
    }

    public static function cmp_obj_club($inscrito_a, $inscrito_b)
    {
        $evento = $inscrito_a->torneio->evento;
        if ($inscrito_a->cidade_id == $inscrito_b->cidade_id) {
            if ($inscrito_a->clube_id == $inscrito_b->clube_id) {
                return InscricaoGerenciarController::cmp_obj_alf($inscrito_a, $inscrito_b);
            } else {
                if ($inscrito_a->clube_id == null || $inscrito_b->clube_id == null) {
                    return InscricaoGerenciarController::cmp_obj_alf($inscrito_a, $inscrito_b);
                }
                return strnatcmp($inscrito_a->clube->getName(), $inscrito_b->clube->getName());
            }
        } else {
            return strnatcmp($inscrito_a->cidade->getName(), $inscrito_b->cidade->getName());
        }
    }

    public function report_list_subscriptions($id, $torneio_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();

        $inscritos = array();
        foreach ($inscricoes as $inscricao) {
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController", "cmp_obj"));

        return view("evento.torneio.inscricao.relatorio.inscritos", compact("evento", "torneio", "inscritos"));
    }

    public function report_list_subscriptions_alf($id, $torneio_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();

        $inscritos = array();
        foreach ($inscricoes as $inscricao) {
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController", "cmp_obj_alf"));

        return view("evento.torneio.inscricao.relatorio.inscritos", compact("evento", "torneio", "inscritos"));
    }

    public function report_list_subscriptions_cidade_alf($id, $torneio_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricoes = $torneio->inscricoes()->get();

        $inscritos = array();
        foreach ($inscricoes as $inscricao) {
            $inscritos[] = $inscricao;
        }
        usort($inscritos, array("App\Http\Controllers\InscricaoGerenciarController", "cmp_obj_club"));

        return view("evento.torneio.inscricao.relatorio.inscritos", compact("evento", "torneio", "inscritos"));
    }

    public function inscricao($id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $sexos = Sexo::all();
        if ($evento) {
            return view("inscricao.gerenciar.inscricao", compact("evento", "sexos"));
        } else {
            return view("inscricao.gerenciar.naoha");
        }
    }

    public function adicionarNovaInscricao($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        if (
            !$request->has("enxadrista_id") ||
            !$request->has("categoria_id") ||
            !$request->has("cidade_id") ||
            !$request->has("evento_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        } elseif (
            $request->input("enxadrista_id") == null || $request->input("enxadrista_id") == "" ||
            $request->input("categoria_id") == null || $request->input("categoria_id") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == "" ||
            $request->input("evento_id") == null || $request->input("evento_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        }

        $inscricao = new Inscricao;
        $torneio = null;
        $evento = Evento::find($request->input("evento_id"));

        foreach ($evento->campos_obrigatorios() as $campo) {
            if (
                !$request->has("campo_personalizado_" . $campo->id)
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            } elseif (
                $request->input("campo_personalizado_" . $campo->id) == null || $request->input("campo_personalizado_" . $campo->id) == ""
            ) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
            }
        }

        foreach ($evento->torneios->all() as $Torneio) {
            foreach ($Torneio->categorias->all() as $categoria) {
                if ($categoria->categoria_id == $request->input("categoria_id")) {
                    $torneio = $Torneio;
                }
            }
        }
        if (!$torneio) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Ocorreu um erro inesperado de pesquisa de Torneio. Por favor, tente novamente mais tarde."]);
        }
        $temInscricao = $evento->torneios()->whereHas("inscricoes", function ($q) use ($request) {
            $q->where([["enxadrista_id", "=", $request->input("enxadrista_id")]]);
        })->first();
        if (count($temInscricao) > 0) {
            $inscricao = Inscricao::where([["enxadrista_id", "=", $request->input("enxadrista_id")], ["torneio_id", "=", $temInscricao->id]])->first();
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você já possui inscrição para este evento!<br/> Categoria: " . $inscricao->categoria->name . "<br/> Caso queira efetuar alguma alteração, favor enviar via email para circuitoxadrezcascavel@gmail.com."]);
        }

        $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
        $categoria = Categoria::find($request->input("categoria_id"));
        if ($categoria) {
            if ($categoria->idade_minima) {
                if (!($categoria->idade_minima <= $enxadrista->howOld())) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Não possui idade mínima."]);
                }
            }
            if ($categoria->idade_maxima) {
                if (!($categoria->idade_maxima >= $enxadrista->howOld())) {
                    return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Idade ultrapassa a máxima."]);
                }
            }
        }

        $inscricao->torneio_id = $torneio->id;
        $inscricao->enxadrista_id = $enxadrista->id;
        $inscricao->categoria_id = $categoria->id;
        $inscricao->cidade_id = $request->input("cidade_id");
        if ($request->has("clube_id")) {
            if ($request->input("clube_id") > 0) {
                $inscricao->clube_id = $request->input("clube_id");
            }
        }
        if ($request->has("confirmado")) {
            $inscricao->confirmado = true;
        }
        $inscricao->regulamento_aceito = true;
        $inscricao->save();

        foreach ($evento->campos() as $campo) {
            if ($request->has("campo_personalizado_" . $campo->id)) {
                if ($request->input("campo_personalizado_" . $campo->id) != "") {
                    $opcao_inscricao = new CampoPersonalizadoOpcaoInscricao;
                    $opcao_inscricao->inscricao_id = $inscricao->id;
                    $opcao_inscricao->opcaos_id = $request->input("campo_personalizado_" . $campo->campo->id);
                    $opcao_inscricao->campo_personalizados_id = $campo->campo->id;
                    $opcao_inscricao->save();
                }
            }
        }

        if ($request->has("atualizar_cadastro")) {
            $enxadrista->cidade_id = $inscricao->cidade_id;
            if ($request->has("clube_id")) {
                if ($request->input("clube_id") > 0) {
                    $enxadrista->clube_id = $request->input("clube_id");
                } else {
                    $enxadrista->clube_id = null;
                }
            } else {
                $enxadrista->clube_id = null;
            }
            $enxadrista->save();

            if ($inscricao->id > 0) {
                if ($inscricao->confirmado) {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 1, "confirmed" => 1]);
                } else {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 1, "confirmed" => 0]);
                }
            } else {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "updated" => 1]);
            }
        } else {
            if ($inscricao->id > 0) {
                if ($inscricao->confirmado) {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 0, "confirmed" => 1]);
                } else {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 0, "confirmed" => 0]);
                }
            } else {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "updated" => 0]);
            }
        }
    }

    public function adicionarNovoEnxadrista($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        if (
            !$request->has("name") ||
            !$request->has("born") ||
            !$request->has("sexos_id") ||
            !$request->has("cidade_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("born") == null || $request->input("born") == "" ||
            $request->input("sexos_id") == null || $request->input("sexos_id") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!<br/><br/><strong>Observação</strong>: TODOS os Campos com <strong>*</strong> SÃO OBRIGATÓRIOS!", "registred" => 0]);
        }

        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O e-mail é inválido. Por favor, verifique e tente novamente.", "registred" => 0]);
        }

        // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
        $nome_corrigido = "";

        $part_names = explode(" ", mb_strtoupper($request->input("name")));
        foreach ($part_names as $part_name) {
            if ($part_name != ' ') {
                $trim = trim($part_name);
                if (strlen($trim) > 0) {
                    $nome_corrigido .= $trim;
                    $nome_corrigido .= " ";
                }
            }
        }
        $nome_corrigido = trim($nome_corrigido);

        $enxadrista = new Enxadrista;
        if (!$enxadrista->setBorn($request->input("born"))) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "A data de nascimento é inválida.", "registred" => 0]);
        }

        $temEnxadrista = Enxadrista::where([["name", "=", $nome_corrigido], ["born", "=", $enxadrista->born]])->first();
        if (count($temEnxadrista) > 0) {
            if ($temEnxadrista->clube) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!", "registred" => 1, "enxadrista_id" => $temEnxadrista->id, "enxadrista_name" => $temEnxadrista->name . " | " . $temEnxadrista->getBorn(), "cidade" => ["id" => $temEnxadrista->cidade->id, "name" => $temEnxadrista->cidade->name], "clube" => ["id" => $temEnxadrista->clube->id, "name" => $temEnxadrista->clube->name]]);
            } else {
                return response()->json([
                    "ok" => 0,
                    "error" => 1,
                    "message" => "Você já possui cadastro! Você será direcionado(a) à próxima etapa da inscrição!",
                    "registred" => 1,
                    "enxadrista_id" => $temEnxadrista->id,
                    "enxadrista_name" => $temEnxadrista->name . " | " . $temEnxadrista->getBorn(),
                    "cidade" => ["id" => $temEnxadrista->cidade->id,
                        "name" => $temEnxadrista->cidade->name],
                    "clube" => ["id" => 0],
                ]);
            }
        }

        $enxadrista->name = $nome_corrigido;
        $enxadrista->cidade_id = $request->input("cidade_id");
        $enxadrista->sexos_id = $request->input("sexos_id");
        if ($request->has("cbx_id")) {
            if ($request->input("cbx_id") > 0) {
                $enxadrista->cbx_id = $request->input("cbx_id");
            }
        }
        if ($request->has("fide_id")) {
            if ($request->input("fide_id") > 0) {
                $enxadrista->fide_id = $request->input("fide_id");
            }
        }
        if ($request->has("lbx_id")) {
            if ($request->input("lbx_id") > 0) {
                $enxadrista->lbx_id = $request->input("lbx_id");
            }
        }
        if ($request->has("email")) {
            if ($request->input("email") != "") {
                $enxadrista->email = $request->input("email");
            }
        }
        if ($request->has("celular")) {
            if ($request->input("celular") != "") {
                $enxadrista->celular = $request->input("celular");
            }
        }
        if ($request->has("clube_id")) {
            if ($request->input("clube_id") > 0) {
                $enxadrista->clube_id = $request->input("clube_id");
            }
        }
        $enxadrista->save();
        if ($enxadrista->id > 0) {
            if ($enxadrista->clube) {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $enxadrista->id, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => $enxadrista->clube->id, "name" => $enxadrista->clube->name]]);
            } else {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista_id" => $enxadrista->id, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => 0]]);
            }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function adicionarNovaCidade($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        if (
            !$request->has("name")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O campo obrigatório não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "O campo obrigatório não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        }
        $cidade = new Cidade;

        $temCidade = Cidade::where([["name", "=", mb_strtoupper($request->input("name"))]])->first();
        if (count($temCidade) > 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Esta cidade já está cadastrada! Selecionamos ela para você.", "registred" => 1, "cidade" => ["id" => $temCidade->id, "name" => $temCidade->name]]);
        }

        $cidade->name = mb_strtoupper($request->input("name"));
        $cidade->save();
        if ($cidade->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "cidade" => ["id" => $cidade->id, "name" => $cidade->name]]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function adicionarNovoClube($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        if (
            !$request->has("name") || !$request->has("cidade_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!", "registred" => 0]);
        }
        $clube = new Clube;

        $temClube = Clube::where([["name", "=", mb_strtoupper($request->input("name"))], ["cidade_id", "=", $request->input("cidade_id")]])->first();
        if (count($temClube) > 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este clube já está cadastrado! Selecionamos ele para você.", "registred" => 1, "clube" => ["id" => $temClube->id, "name" => $temClube->name]]);
        }

        $clube->name = mb_strtoupper($request->input("name"));
        $clube->cidade_id = mb_strtoupper($request->input("cidade_id"));
        $clube->save();
        if ($clube->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "clube" => ["id" => $clube->id, "name" => $clube->name]]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }

    public function buscaEnxadrista($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["results" => [], "pagination" => true]);
        }

        $enxadristas = Enxadrista::where([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])->orderBy("name", "ASC")->get();
        $results = array();
        foreach ($enxadristas as $enxadrista) {
            if ($enxadrista->estaInscrito($request->input("evento_id"))) {
                $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name . " | " . $enxadrista->getBorn() . " - Já Está Inscrito neste Evento");
            } else {
                $results[] = array("id" => $enxadrista->id, "text" => $enxadrista->name . " | " . $enxadrista->getBorn());
            }

        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function getCidadeClube($id, $enxadrista_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        $enxadrista = Enxadrista::find($enxadrista_id);
        if ($enxadrista) {
            if ($enxadrista->clube) {
                return response()->json(["ok" => 1, "error" => 0, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => $enxadrista->clube->id, "name" => $enxadrista->clube->name]]);
            } else {
                return response()->json(["ok" => 1, "error" => 0, "cidade" => ["id" => $enxadrista->cidade->id, "name" => $enxadrista->cidade->name], "clube" => ["id" => 0]]);
            }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Não há enxadrista com esse código!"]);
        }
    }

    public function buscaCategoria($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["results" => [], "pagination" => true]);
        }

        $enxadrista = Enxadrista::find($request->input("enxadrista_id"));
        $categorias = $evento->categorias()->whereHas("categoria", function ($QUERY) use ($request, $enxadrista) {
            $QUERY->where([
                ["name", "like", "%" . $request->input("q") . "%"],
            ])
                ->where(function ($query) use ($enxadrista) {
                    $query->where(function ($q) use ($enxadrista) {
                        $q->where([["idade_minima", "<=", $enxadrista->howOld()]]);
                        $q->where([["idade_maxima", ">=", $enxadrista->howOld()]]);
                    })
                        ->orWhere(function ($q) use ($enxadrista) {
                            $q->where([["idade_minima", "<=", $enxadrista->howOld()]]);
                            $q->where([["idade_maxima", "=", null]]);
                        })
                        ->orWhere(function ($q) use ($enxadrista) {
                            $q->where([["idade_minima", "=", null]]);
                            $q->where([["idade_maxima", ">=", $enxadrista->howOld()]]);
                        })
                        ->orWhere(function ($q) {
                            $q->where([["idade_minima", "=", null]]);
                            $q->where([["idade_maxima", "=", null]]);
                        });
                })
                ->where(function ($q2) use ($enxadrista) {
                    $q2->where(function ($q3) use ($enxadrista) {
                        if ($enxadrista->sexos_id) {
                            $q3->where(function ($q4) use ($enxadrista) {
                                $q4->whereHas("sexos", function ($q5) use ($enxadrista) {
                                    $q5->where([["sexos_id", "=", $enxadrista->sexos_id]]);
                                });
                            });
                            $q3->orWhere(function ($q4) {
                                $q4->doesntHave("sexos");
                            });
                        } else {
                            $q3->whereDoesntHave("sexos");
                        }
                    });
                });
        })
            ->get();
        $results = array();
        foreach ($categorias as $categoria) {
            $results[] = array("id" => $categoria->categoria->id, "text" => $categoria->categoria->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function buscaCidade($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["results" => [], "pagination" => true]);
        }

        $cidades = Cidade::where([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])->get();
        $results = array();
        foreach ($cidades as $cidade) {
            $results[] = array("id" => $cidade->id, "text" => $cidade->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function buscaClube($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["results" => [], "pagination" => true]);
        }

        $clubes = Clube::where([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])->orWhere(function ($q) use ($request) {
            $q->whereHas("cidade", function ($Q) use ($request) {
                $Q->where([
                    ["name", "like", "%" . $request->input("q") . "%"],
                ]);
            });
        })->get();
        $results = array(array("id" => -1, "text" => "Sem Clube"));
        foreach ($clubes as $clube) {
            $results[] = array("id" => $clube->id, "text" => $clube->cidade->name . " - " . $clube->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function confirmacao($id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        if ($evento) {
            return view("inscricao.gerenciar.confirmar", compact("evento"));
        } else {
            return view("inscricao.gerenciar.naoha");
        }
    }
    public function buscaEnxadristaParaConfirmacao($id, Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["results" => [], "pagination" => true]);
        }

        $inscricoes = Inscricao::where(function ($q1) use ($id, $request) {
            $q1->whereHas("enxadrista", function ($q2) use ($request) {
                $q2->where([
                    ["name", "like", "%" . $request->input("q") . "%"],
                ])->orderBy("name", "ASC");
            });
            $q1->whereHas("torneio", function ($q2) use ($id, $request) {
                $q2->where([
                    ["evento_id", "=", $id],
                ]);
            });
            $q1->where([
                ["confirmado", "=", false],
            ]);
        })
            ->orderBy("id", "ASC")
            ->get();
        $results = array();
        foreach ($inscricoes as $inscricao) {
            $results[] = array("id" => $inscricao->id, "text" => "[#" . $inscricao->id . "] " . $inscricao->enxadrista->name . " | " . $inscricao->enxadrista->getBorn());
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }

    public function getInscricaoDados($id, $inscricao_id)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        $inscricao = Inscricao::find($inscricao_id);
        if ($inscricao) {
            if ($inscricao->clube) {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista" => ["id" => $inscricao->enxadrista->id], "cidade" => ["id" => $inscricao->cidade->id, "name" => $inscricao->cidade->name], "categoria" => ["id" => $inscricao->categoria->id, "name" => $inscricao->categoria->name], "clube" => ["id" => $inscricao->clube->id, "name" => $inscricao->clube->name]]);
            } else {
                return response()->json(["ok" => 1, "error" => 0, "enxadrista" => ["id" => $inscricao->enxadrista->id], "cidade" => ["id" => $inscricao->cidade->id, "name" => $inscricao->cidade->name], "categoria" => ["id" => $inscricao->categoria->id, "name" => $inscricao->categoria->name], "clube" => ["id" => 0]]);
            }
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Não há enxadrista com esse código!"]);
        }
    }

    public function confirmarInscricao($id,Request $request)
    {
        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [4,5]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Você não possui permissão para fazer isto.", "registred" => 0]);
        }

        if (
            !$request->has("inscricao_id") || !$request->has("categoria_id") || !$request->has("cidade_id") || !$request->has("evento_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("inscricao_id") == null || $request->input("inscricao_id") == "" ||
            $request->input("categoria_id") == null || $request->input("categoria_id") == "" ||
            $request->input("cidade_id") == null || $request->input("cidade_id") == "" ||
            $request->input("evento_id") == null || $request->input("evento_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
        }

        $inscricao = Inscricao::find($request->input("inscricao_id"));
        if (!$inscricao) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Não existe um inscrição com o código informado!"]);
        }
        if ($inscricao->confirmado) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "A inscrição já está confirmada!"]);
        }
        $torneio = null;
        if ($request->input("categoria_id") != $inscricao->categoria_id) {
            $evento = Evento::find($request->input("evento_id"));

            foreach ($evento->torneios->all() as $Torneio) {
                foreach ($Torneio->categorias->all() as $categoria) {
                    if ($categoria->categoria_id == $request->input("categoria_id")) {
                        $torneio = $Torneio;
                    }
                }
            }
            if (!$torneio) {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Ocorreu um erro inesperado de pesquisa de Torneio. Por favor, tente novamente mais tarde."]);
            }

            $categoria = Categoria::find($request->input("categoria_id"));
            if ($categoria) {
                if ($categoria->idade_minima) {
                    if (!($categoria->idade_minima <= $enxadrista->howOld())) {
                        return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Não possui idade mínima."]);
                    }
                }
                if ($categoria->idade_maxima) {
                    if (!($categoria->idade_maxima >= $enxadrista->howOld())) {
                        return response()->json(["ok" => 0, "error" => 1, "message" => "Você não está apto a participar da categoria que você enviou! Motivo: Idade ultrapassa a máxima."]);
                    }
                }
            }
            $inscricao->categoria_id = $categoria->id;
            $inscricao->torneio_id = $torneio->id;
        }

        if ($inscricao->cidade_id != $request->input("cidade_id")) {
            $inscricao->cidade_id = $request->input("cidade_id");
        }

        if ($inscricao->clube_id != $request->input("clube_id")) {
            if ($request->has("clube_id")) {
                if ($request->input("clube_id") > 0) {
                    $inscricao->clube_id = $request->input("clube_id");
                }
            }
        }

        $inscricao->confirmado = true;
        $inscricao->regulamento_aceito = true;
        $inscricao->save();

        if ($request->has("atualizar_cadastro")) {
            $enxadrista = Enxadrista::find($inscricao->enxadrista_id);
            $enxadrista->cidade_id = $inscricao->cidade_id;
            if ($request->has("clube_id")) {
                if ($request->input("clube_id") > 0) {
                    $enxadrista->clube_id = $request->input("clube_id");
                } else {
                    $enxadrista->clube_id = null;
                }
            } else {
                $enxadrista->clube_id = null;
            }
            $enxadrista->save();

            if ($inscricao->id > 0) {
                if ($inscricao->confirmado) {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 1, "confirmed" => 1]);
                } else {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 1, "confirmed" => 0]);
                }
            } else {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "updated" => 1]);
            }
        } else {
            if ($inscricao->id > 0) {
                if ($inscricao->confirmado) {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 0, "confirmed" => 1]);
                } else {
                    return response()->json(["ok" => 1, "error" => 0, "updated" => 0, "confirmed" => 0]);
                }
            } else {
                return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "updated" => 0]);
            }
        }
    }



    public function sendWhatsappMessage($id, $torneio_id, $inscricao_id)
    {

        $user = Auth::user();
        $evento = Evento::find($id);
        if (
            !$user->hasPermissionGlobal() &&
            !$user->hasPermissionEventByPerfil($evento->id, [3, 4]) &&
            !$user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [6,7])
        ) {
            return redirect("/evento/dashboard/".$evento->id);
        }

        $torneio = Torneio::find($torneio_id);
        $inscricao = Inscricao::find($inscricao_id);

        if(!$inscricao->is_whatsapp_sent){
            $sid = env("TWILIO_SID");
            $token = env("TWILIO_TOKEN");
            if($sid && $token){
                $twilio = new Client($sid, $token);

                $message = $twilio->messages
                    ->create("whatsapp:+554598547889", // to
                        array(
                            "from" => "whatsapp:+14155238886",
                            "body" => "Your appointment is coming up on July 21 at 3PM",
                        )
                    );

                print($message->sid);
            }

        }

        return redirect("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes");
    }
}
