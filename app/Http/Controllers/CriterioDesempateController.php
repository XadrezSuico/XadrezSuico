<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;

class CriterioDesempateController extends Controller
{
    public function generate($evento, $enxadrista, $criterio_desempate)
    {
        Log::debug("Gerador de Critérios de Desempate de Torneios");
        Log::debug("Código do Critério a ser Gerado: ".$criterio_desempate->internal_code);
        switch ($criterio_desempate->internal_code) {
            case "TT3_1":
                Log::debug("Código TT3_1");
                return $this->generate_tt3_1($evento, $enxadrista);
                break;
            case "TT3_2":
                Log::debug("Código TT3_2");
                return $this->generate_tt3_2($evento, $enxadrista);
                break;
        }
        Log::debug("Código não encontrado");
        return false;
    }


    // CRITÉRIOS DE DESEMPATE

    // CÓDIGO: TT3_1
    // NOME DO CRITÉRIO: RESULTADO DA FINAL DO TORNEIO DE TIPO CHAVE SEMI-FINAL/FINAL
    public function generate_tt3_1($evento, $enxadrista)
    {
        $valor = 0;
        $inscricao = $enxadrista->getInscricao($evento->id);
        Log::debug("Gerando critério desempate de código TT3_1 para o enxadrista ".$enxadrista->id." inscrito no evento ".$evento->id);

        if($inscricao){
            Log::debug("Inscrição: ".$inscricao->uuid);
            if($inscricao->torneio_id){
                if($inscricao->torneio->tipo_torneio->id == 3){
                    $rodada_2 = $inscricao->torneio->rodadas()->where([["numero","=",2]])->first();
                    if($rodada_2){
                        $emparceiramento = $rodada_2->emparceiramentos->first();
                        if($emparceiramento){
                            if(
                                $emparceiramento->inscricao_a == $inscricao->id
                                ||
                                $emparceiramento->inscricao_b == $inscricao->id
                            ){
                                if($emparceiramento->resultado == -1 || $emparceiramento->resultado == 1){
                                    if(
                                        $emparceiramento->inscricao_a == $inscricao->id
                                    ){
                                        if($emparceiramento->resultado == -1){
                                            $valor = 2;
                                        }else{
                                            $valor = 1;
                                        }
                                    }elseif(
                                        $emparceiramento->inscricao_b == $inscricao->id
                                    ){
                                        if($emparceiramento->resultado == 1){
                                            $valor = 2;
                                        }else{
                                            $valor = 1;
                                        }
                                    }else{
                                        Log::debug("Erro: Não há a inscrição no emparceiramento.");
                                    }
                                }elseif($emparceiramento->resultado == 0){
                                    $armageddon = $emparceiramento->armageddons->first();
                                    if($armageddon){
                                        if(
                                            $armageddon->inscricao_a == $inscricao->id
                                        ){
                                            if($armageddon->resultado == -1){
                                                $valor = 2;
                                            }else{
                                                $valor = 1;
                                            }
                                        }elseif(
                                            $armageddon->inscricao_b == $inscricao->id
                                        ){
                                            if($armageddon->resultado == 1){
                                                $valor = 2;
                                            }else{
                                                $valor = 1;
                                            }
                                        }else{
                                            Log::debug("Erro: Não há a inscrição no armageddon.");
                                        }
                                    }else{
                                        Log::debug("Erro: A partida terminou empatada, porém não tem desempate.");
                                    }
                                }
                            }else{
                                Log::debug("Erro: A inscrição não está no emparceiramento final.");
                            }
                        }else{
                            Log::debug("Erro: Emparceiramento final não encontrado.");
                        }
                    }else{
                        Log::debug("Erro: Rodada 2 não encontrada.");
                    }
                }else{
                    Log::debug("Erro: Tipo de Torneio não encontrado.");
                }
            }else{
                Log::debug("Erro: Torneio não encontrado.");
            }
        }else{
            Log::debug("Erro: Inscrição não encontrada.");
        }
        if(!is_bool($valor)){
            Log::debug("Critério gerado: ".$valor);
        }

        return $valor;
    }


    // CRITÉRIOS DE DESEMPATE

    // CÓDIGO: TT3_2
    // NOME DO CRITÉRIO: DEFINIÇÃO DE 3o LUGAR DE ACORDO COM O 1o LUGAR (QUEM JOGOU COM O 3o LUGAR NA SEMI-FINAL FICA EM 3o)
    public function generate_tt3_2($evento, $enxadrista)
    {
        $valor = 0;
        $inscricao = $enxadrista->getInscricao($evento->id);
        Log::debug("Gerando critério desempate de código TT3_1 para o enxadrista ".$enxadrista->id." inscrito no evento ".$evento->id);

        if($inscricao){
            Log::debug("Inscrição: ".$inscricao->uuid);
            if($inscricao->torneio_id){
                if($inscricao->torneio->tipo_torneio->id == 3){
                    $vencedor_count = $inscricao->torneio->inscricoes()->whereHas("criterios_desempate",function($q1){
                        $q1->whereHas("criterio",function($q2){
                            $q2->where([["internal_code","=","TT3_1"]]);
                        })
                        ->where([["valor","=",1]]);
                    })->count();
                    if($vencedor_count > 0){
                        $vencedor = $inscricao->torneio->inscricoes()->whereHas("criterios_desempate",function($q1){
                            $q1->whereHas("criterio",function($q2){
                                $q2->where([["internal_code","=","TT3_1"]]);
                            })
                            ->where([["valor","=",2]]);
                        })->first();
                        $vice = $inscricao->torneio->inscricoes()->whereHas("criterios_desempate",function($q1){
                            $q1->whereHas("criterio",function($q2){
                                $q2->where([["internal_code","=","TT3_1"]]);
                            })
                            ->where([["valor","=",1]]);
                        })->first();
                        if(
                            $vencedor->id != $inscricao->id
                            &&
                            $vice->id != $inscricao->id
                        ){
                            foreach($inscricao->getEmparceiramentos() as $emparceiramento){
                                if(!$emparceiramento->is_armageddon){
                                    if($emparceiramento->rodada->numero == 1){
                                        if($emparceiramento->inscricao_a == $vencedor->id || $emparceiramento->inscricao_b == $vencedor->id){
                                            $valor = 2;
                                        }else{
                                            $valor = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        Log::debug("Erro: Não foi encontrado o vencedor do torneio.");
                    }
                }else{
                    Log::debug("Erro: Tipo de Torneio não encontrado.");
                }
            }else{
                Log::debug("Erro: Torneio não encontrado.");
            }
        }else{
            Log::debug("Erro: Inscrição não encontrada.");
        }
        if(!is_bool($valor)){
            Log::debug("Critério gerado: ".$valor);
        }

        return $valor;
    }
}
