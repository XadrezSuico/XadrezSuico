<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\Inscricao;
use App\Enum\EmailType;

use Log;

class InscricaoLichessController extends Controller
{
    public function index($uuid, Request $request)
    {
        $passo = 1;
        $inscricao = Inscricao::where([["uuid","=",$uuid]])->first();
        if ($inscricao) {
            if (!$inscricao->torneio->evento->isLichessDelayToEnter()) {
                $request->session()->forget('state');
                $request->session()->forget('uuid');
                $request->session()->forget('lichess_token');
                $request->session()->forget('lichess_token_timeout');
            }
            if(!$inscricao->torneio->evento->is_lichess_integration){
                return redirect("/inscricao/".$$inscricao->torneio->evento->id);
            }
            if($inscricao->is_lichess_found){
                $passo = 3;
                return view("inscricao.inscricao_lichess", compact("inscricao","passo"));
            }
            if($request->session()->has('lichess_token') && $request->session()->has('lichess_token_timeout')){
                if($request->session()->get('lichess_token_timeout',time() - 1) >= time()){
                    $passo = 2;

                    $user_response = \Httpful\Request::get('https://lichess.org/api/account')
                            ->expectsJson()
                            ->addHeader('Authorization', "Bearer " . $request->session()->get('lichess_token',""))
                            ->send();
                    if ($user_response->code == 200) {
                        $username = $user_response->body->username;


                        return view("inscricao.inscricao_lichess", compact("inscricao","passo","username"));
                    }
                }
            }



            return view("inscricao.inscricao_lichess", compact("inscricao","passo"));
        } else {
            return view("inscricao.naoha");
        }
    }

    public function redirect($uuid, Request $request)
    {
        $inscricao = Inscricao::where([["uuid","=",$uuid]])->first();
        if ($inscricao) {
            if(!$inscricao->torneio->evento->is_lichess_integration){
                return redirect("/inscricao/".$$inscricao->torneio->evento->id);
            }

            $request->session()->put('state', $state = $this->base64url_encode(pack('H*',bin2hex(openssl_random_pseudo_bytes(32)))));
            $request->session()->put('uuid', $uuid);

            $query = http_build_query([
                'response_type' => 'code',
                'client_id' => $request->getHttpHost(),
                'redirect_uri' => url("/inscricao/".$uuid."/lichess/callback"),
                'scope' => "preference:read tournament:write team:write",
                'code_challenge_method' => "S256",
                'code_challenge' => $this->base64url_encode(pack('H*', hash("sha256",$state))),
            ]);

            return redirect('https://lichess.org/oauth?' . $query);

        } else {
            return view("inscricao.naoha");
        }
    }

    public function callback($uuid, Request $request)
    {
        $state = $request->session()->get('state');
        $response = \Httpful\Request::post('https://lichess.org/api/token')
            ->sendsType(\Httpful\Mime::FORM)
            ->body('grant_type=authorization_code&client_id=' . $request->getHttpHost() .
                "&redirect_uri=" . url("/inscricao/".$uuid."/lichess/callback") .
                "&code=" . $request->input("code") .
                "&code_verifier=" . $state)
            ->expectsJson()
            ->send();
        if ($response->code == 200) {
            $user_response = \Httpful\Request::get('https://lichess.org/api/account')
                    ->expectsJson()
                    ->addHeader('Authorization', "Bearer " . $response->body->access_token)
                    ->send();
            if ($user_response->code == 200) {
                $request->session()->put('lichess_token', $response->body->access_token);
                $request->session()->put('lichess_token_timeout', time() + (5*60));

                $request->session()->forget('state');
                $request->session()->forget('uuid');

                return redirect("/inscricao/".$uuid."/lichess");
            }

        }

    }
    public function confirm($uuid, Request $request)
    {
        $inscricao = Inscricao::where([["uuid","=",$uuid]])->first();
        if ($inscricao) {
            if(!$inscricao->torneio->evento->is_lichess_integration){
                return redirect("/inscricao/".$$inscricao->torneio->evento->id);
            }
            if (!$inscricao->torneio->evento->isLichessDelayToEnter()) {
                $request->session()->forget('state');
                $request->session()->forget('uuid');
                $request->session()->forget('lichess_token');
                $request->session()->forget('lichess_token_timeout');

                return redirect("/inscricao/" . $uuid . "/lichess");
            }

            if($request->session()->has('lichess_token') && $request->session()->has('lichess_token_timeout')){
                if($request->session()->get('lichess_token_timeout',time() - 1) >= time()){
                    $user_response = \Httpful\Request::get('https://lichess.org/api/account')
                            ->expectsJson()
                            ->addHeader('Authorization', "Bearer " . $request->session()->get('lichess_token',""))
                            ->send();
                    if ($user_response->code == 200) {
                        $username = $user_response->body->username;


                        $inscricao->enxadrista->lichess_username = mb_strtolower($username);
                        $inscricao->enxadrista->save();

                        if($inscricao->torneio->evento->lichess_team_password){
                            if($inscricao->torneio->evento->lichess_team_password){
                                $team_response = \Httpful\Request::post('https://lichess.org/team/'.$inscricao->torneio->evento->lichess_team_id.'/join')
                                    ->sendsType(\Httpful\Mime::FORM)
                                    ->body('password=' . $inscricao->torneio->evento->lichess_team_password)
                                    ->expectsJson()
                                    ->addHeader('Authorization', "Bearer " . $request->session()->get('lichess_token', ""))
                                    ->send();
                            }else{
                                $team_response = \Httpful\Request::post('https://lichess.org/team/'.$inscricao->torneio->evento->lichess_team_id.'/join')
                                    ->expectsJson()
                                    ->addHeader('Authorization', "Bearer " . $request->session()->get('lichess_token', ""))
                                    ->send();
                            }
                        }else{
                            $team_response = \Httpful\Request::post('https://lichess.org/team/'.$inscricao->torneio->evento->lichess_team_id.'/join')
                                ->expectsJson()
                                ->addHeader('Authorization', "Bearer " . $request->session()->get('lichess_token', ""))
                                ->send();
                        }
                        if($team_response->code == 200){
                            if($team_response->body->ok){
                                if($inscricao->torneio->evento->lichess_tournament_password){
                                    $tournament_response = \Httpful\Request::post('https://lichess.org/api/swiss/'.$inscricao->torneio->evento->lichess_tournament_id.'/join')
                                        ->sendsType(\Httpful\Mime::FORM)
                                        ->body('password=' . $inscricao->torneio->evento->lichess_tournament_password)
                                        ->expectsJson()
                                        ->addHeader('Authorization', "Bearer " . $request->session()->get('lichess_token', ""))
                                        ->send();
                                }else{
                                    $tournament_response = \Httpful\Request::post('https://lichess.org/api/swiss/'.$inscricao->torneio->evento->lichess_tournament_id.'/join')
                                        ->expectsJson()
                                        ->addHeader('Authorization', "Bearer " . $request->session()->get('lichess_token', ""))
                                        ->send();
                                }
                                if($tournament_response->code == 200){
                                    if($tournament_response->body->ok){
                                        Log::debug("Inscrição efetuada.");

                                        $lichess_integration_controller = new LichessIntegrationController;
                                        $retorno = $lichess_integration_controller->getSwissResults($inscricao->torneio->evento->lichess_tournament_id);
                                        if($retorno["ok"] == 1){
                                            Log::debug("Pesquisa dos enxadristas inscritos");
                                            foreach(explode("\n",$retorno["data"]) as $lichess_player_raw){
                                                $lichess_player = json_decode(trim($lichess_player_raw));
                                                if(isset($lichess_player->username)){
                                                    Log::debug("Username: ".$lichess_player->username);
                                                    if($lichess_player->username == $inscricao->enxadrista->lichess_username){
                                                        Log::debug("Encontrado.");

                                                        if (!$inscricao->is_lichess_found && !$inscricao->is_last_lichess_found) {
                                                            EmailController::schedule(
                                                                $inscricao->enxadrista->email,
                                                                $inscricao,
                                                                EmailType::ConfirmacaoInscricaoLichess,
                                                                $inscricao->enxadrista
                                                            );
                                                        }

                                                        $inscricao->is_lichess_found = true;
                                                        $inscricao->is_last_lichess_found = true;
                                                        $inscricao->save();
                                                        // return redirect($inscricao->torneio->evento->getLichessTournamentLink());

                                                    }
                                                }
                                            }

                                        }
                                    }
                                }
                            }
                        }

                    }
                }
            }



            return redirect("/inscricao/".$uuid."/lichess");
        } else {
            return view("inscricao.naoha");
        }
    }
    public function clear($uuid, Request $request)
    {
        $request->session()->forget('state');
        $request->session()->forget('uuid');
        $request->session()->forget('lichess_token');
        $request->session()->forget('lichess_token_timeout');
        return redirect("/inscricao/".$uuid."/lichess");
    }



    function base64url_encode($plainText)
    {
        $base64 = base64_encode($plainText);
        $base64 = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');
        return ($base64url);
    }

}
