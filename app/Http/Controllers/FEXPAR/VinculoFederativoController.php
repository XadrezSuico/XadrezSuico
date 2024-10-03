<?php

namespace App\Http\Controllers\FEXPAR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helper\IPHelper;

use App\Vinculo;
use App\VinculoConsulta;

use Auth;


class VinculoFederativoController extends Controller
{
    public function vinculos($year = null){
        if($year == null) $year = date("Y");
        if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
            $vinculos = Vinculo::where([
                ["ano","=", $year],
                ["is_confirmed_system","=",true],
            ])
            ->orWhere([
                ["ano","=", $year],
                ["is_confirmed_manually","=",true],
            ])
            ->get();

            return view("paginas_especiais.fexpar.vinculos",compact("vinculos","year"));
        }
        return abort(404);
    }
    public function vinculo($uuid){
        if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
            if(Vinculo::where([
                ["ano","=",date("Y")],
                ["uuid","=",$uuid],
            ])
            ->count() > 0){
                $vinculo = Vinculo::where([
                    ["ano","=",date("Y")],
                    ["uuid","=",$uuid],
                ])
                ->first();

                $ip = IPHelper::getIp();

                $vinculo_consulta = new VinculoConsulta;
                $vinculo_consulta->ip = $ip;
                $vinculo_consulta->vinculos_uuid = $vinculo->uuid;
                $vinculo_consulta->vinculos_id = $vinculo->id;
                $vinculo_consulta->enxadrista_id = $vinculo->enxadrista_id;
                $vinculo_consulta->ano = $vinculo->ano;
                $vinculo_consulta->cidade_id = $vinculo->cidade_id;
                $vinculo_consulta->clube_id = $vinculo->clube_id;
                $vinculo_consulta->is_confirmed_system = $vinculo->is_confirmed_system;
                $vinculo_consulta->is_confirmed_manually = $vinculo->is_confirmed_manually;
                $vinculo_consulta->system_inscricoes_in_this_club_confirmed = $vinculo->system_inscricoes_in_this_club_confirmed;
                $vinculo_consulta->events_played = $vinculo->events_played;
                $vinculo_consulta->obs = $vinculo->obs;
                $vinculo_consulta->vinculated_at = $vinculo->vinculated_at;
                $vinculo_consulta->save();

                return redirect("/especiais/fexpar/vinculos/consulta/".$vinculo_consulta->uuid);
            }
        }
        return abort(404);
    }
    public function consulta($uuid){
        if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
            if(VinculoConsulta::where([
                ["uuid","=",$uuid],
            ])
            ->count() > 0){
                $vinculo_consulta = VinculoConsulta::where([
                    ["uuid","=",$uuid],
                ])
                ->first();

                $ip = IPHelper::getIp();

                if(Auth::check()){
                    activity('access_vinculo_consulta')
                    ->causedBy(Auth::user())
                    ->performedOn($vinculo_consulta)
                    ->withProperties(['ip' => $ip,"vinculo_consulta_uuid"=>$vinculo_consulta->uuid])
                    ->log('Acesso à consulta de vínculo efetuada.');
                }else{
                    activity('access_vinculo_consulta')
                    ->performedOn($vinculo_consulta)
                    ->withProperties(['ip' => $ip,"vinculo_consulta_uuid"=>$vinculo_consulta->uuid])
                    ->log('Acesso à consulta de vínculo efetuada.');
                }


                return view("paginas_especiais.fexpar.vinculo_ver",compact("vinculo_consulta","ip"));
            }
        }
        return abort(404);
    }


    public function qrcode($uuid){
        if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
            if(VinculoConsulta::where([
                ["uuid","=",$uuid],
            ])
            ->count() > 0){
                $vinculo_consulta = VinculoConsulta::where([
                    ["uuid","=",$uuid],
                ])
                ->first();
                return $vinculo_consulta->getQrCode();
            }
        }
        return abort(404);
    }

    public function consulta_form(){
        if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
            return view("paginas_especiais.fexpar.consulta");
        }
        return abort(404);
    }
}
