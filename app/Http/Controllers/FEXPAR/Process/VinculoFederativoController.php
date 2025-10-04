<?php

namespace App\Http\Controllers\FEXPAR\Process;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Enxadrista;
use App\Vinculo;

use Log;


class VinculoFederativoController extends Controller
{
    /*
     *
     * THIS FUNCTION VERIFY ALL POSSIBLE CHESS PLAYERS TO HAVE CLUB VINCULATE, TO BEFORE CAN CHECK IF THIS HAPPEN
     *
     */
    public function pre_vinculate(){
        $enxadristas = Enxadrista::where(function($q0){
            // BR
            $q0->where(function($q1){
                $q1->whereHas("documentos", function ($q2) {
                    $q2->where([
                        ["tipo_documentos_id", "=", 1],
                    ]);
                })
                ->whereHas("documentos", function ($q2) {
                    $q2->where([
                        ["tipo_documentos_id", "=", 2],
                    ]);
                });
                $q1->where([["pais_id","=",33]]);
            })
            // FOREIGN
            ->orWhere(function($q1){
                $q1->whereHas("documentos", function ($q2) {
                    $q2->whereIn("tipo_documentos_id", [3,4]);
                });
                $q1->where([["pais_id","!=",33]]);
            });
        })
        ->whereHas("cidade", function ($q1) {
            $q1->whereHas("estado", function ($q2) {
                $q2->where([
                    ["ibge_id", "=", 41],
                ]);
            });
        })
        ->whereHas("clube", function ($q1) {
            $q1->whereHas("cidade", function ($q2) {
                $q2->whereHas("estado", function ($q3) {
                    $q3->where([
                        ["ibge_id", "=", 41],
                    ]);
                });
            });
            $q1->where([["is_fexpar___clube_valido_vinculo_federativo", "=", true]]);
        })
        ->whereHas("inscricoes", function ($q1) {
            $q1->where([
                ["confirmado", "=", true],
                ["is_desclassificado", "=", false],
                ["desconsiderar_pontuacao_geral", "=", false],
                ["desconsiderar_classificado", "=", false],
            ]);
            $q1->whereHas("torneio", function ($q2) {
                $q2->whereHas("evento", function ($q3) {
                    $q3->where([
                        ["data_inicio", ">=", date("Y") . "-01-01"],
                        ["data_fim", "<=", date("Y") . "-12-31"],
                    ])
                    ->where(function($q4){
                        $q4->where([["classificavel","=",true]]);
                        $q4->orWhere([["mostrar_resultados","=",true]]);
                    });
                });
            });
        })
        ->get();

        foreach($enxadristas as $enxadrista){
            if($enxadrista->vinculos()->where([["ano","=",date("Y")],["is_confirmed_manually","=",true]])->count() == 0){
                if($enxadrista->vinculos()->where([["ano","=",date("Y")]])->count() == 0){
                    $vinculo = new Vinculo;
                    $vinculo->ano = date("Y");
                    $vinculo->enxadrista_id = $enxadrista->id;
                    $vinculo->cidade_id = $enxadrista->clube->cidade_id;
                    $vinculo->clube_id = $enxadrista->clube_id;
                    $vinculo->save();
                }
            }
        }
    }


    public function vinculate(){
        $vinculos = Vinculo::where([["ano","=",date("Y")],["is_confirmed_system","=",true]])
        ->orWhere([["ano","=",date("Y")],["is_confirmed_system","=",false],["is_confirmed_manually","=",false]])->get();

        foreach($vinculos as $vinculo){
            $count = $vinculo->enxadrista->inscricoes()
                ->where(function ($q1) use ($vinculo) {
                    $q1->where([
                        ["confirmado", "=", true],
                        ["is_desclassificado", "=", false],
                        ["desconsiderar_pontuacao_geral", "=", false],
                        ["desconsiderar_classificado", "=", false],
                        ["cidade_id","=",$vinculo->cidade_id],
                        ["clube_id","=",$vinculo->clube_id]
                    ])
                    ->whereHas("torneio", function ($q2) {
                        $q2->whereHas("evento", function ($q3) {
                            $q3->where([
                                ["data_inicio", ">=", date("Y") . "-01-01"],
                                ["data_fim", "<=", date("Y") . "-12-31"],
                            ])
                            ->where(function($q4){
                                $q4->where([["classificavel","=",true]]);
                                $q4->orWhere([["mostrar_resultados","=",true]]);
                            });
                        });
                    });
                })->count();
            if($count > 0){
                if($vinculo->clube->is_fexpar___clube_valido_vinculo_federativo){
                    $vinculo->is_confirmed_system = true;
                    $vinculo->system_inscricoes_in_this_club_confirmed = $count;
                    $vinculo->vinculated_at = date("Y-m-d H:i:s");
                    $vinculo->is_efective = true;
                    $vinculo->save();
                    activity()
                    ->performedOn($vinculo)
                    ->log('Vínculo confirmado automaticamente.');
                }

            }else{
                $vinculo->is_confirmed_system = false;
                $vinculo->system_inscricoes_in_this_club_confirmed = 0;
                $vinculo->save();

                activity()
                ->performedOn($vinculo)
                ->log('Este enxadrista não possui inscrição confirmada em nenhum evento pela cidade e clube do mesmo. Com isto, o vínculo não foi confirmado.');
            }

        }
    }
}
