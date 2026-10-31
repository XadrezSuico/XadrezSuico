<?php

namespace App\Http\Controllers\Integration\Online;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use Log;

class ChessComIntegrationController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    private function apiErrorResponse(string $requestUrl, string $slug, int $statusCode, string $body, string $messageKey = "tournament_not_found"): array
    {
        $bodyPreview = trim(preg_replace('/\s+/', ' ', $body) ?? '');

        return [
            "ok" => 0,
            "error" => 1,
            "message_key" => $messageKey,
            "status_code" => $statusCode,
            "request_url" => $requestUrl,
            "slug" => $slug,
            "response_body" => mb_substr($bodyPreview, 0, 100000),
        ];
    }

    public function getTournament($slug){
        if(env("APP_ENV","local") == "local"){
            $client = new Client(['http_errors' => false, "verify"=>false]);
        }else{
            $client = new Client(['http_errors' => false]);
        }
        $requestUrl = "https://api.chess.com/pub/tournament/".$slug;
        $response = $client->get($requestUrl);
        $html = (string) $response->getBody();
        if($response->getStatusCode() == 200){
            $json = json_decode($html);
            if($json->status == "finished"){
                $json_array = json_decode($html,true);

                if(count($json->rounds) > 0){
                    $response_round = $client->get($json->rounds[0]);
                    if($response_round->getStatusCode() == 200){
                        $json_round = json_decode($response_round->getBody());
                        $json_array["last_round"] = json_decode($response_round->getBody(),true);

                        if(isset($json_round->groups)){
                            if(count($json_round->groups) > 0){
                                $response_group = $client->get($json_round->groups[0]);
                                if($response_group->getStatusCode() == 200){
                                    $json_group = json_decode($response_group->getBody());
                                    $json_array["last_round"]["group"] = json_decode($response_group->getBody(),true);
                                }
                            }
                        }
                    }
                }
            }

            return array("ok"=>1,"error"=>0,"data"=>json_encode($json_array));
        }

        return $this->apiErrorResponse($requestUrl, $slug, $response->getStatusCode(), $html);
    }
    public function getResults($slug){
        $client = new Client([
            'http_errors' => false,
            'verify' => env("APP_ENV", "local") != "local",
            'connect_timeout' => 10,
            'timeout' => 30,
        ]);
        $requestUrl = $this->resultsDownloadUrl($slug);
        try {
            $response = $client->get($requestUrl);
            $csv = (string) $response->getBody();
            if ($response->getStatusCode() == 200) {
                return ["ok" => 1, "error" => 0, "data" => $csv];
            }

            return $this->apiErrorResponse($requestUrl, $slug, $response->getStatusCode(), $csv, "results_download_failed");
        } catch (\GuzzleHttp\Exception\TransferException $exception) {
            return $this->apiErrorResponse($requestUrl, $slug, 0, $exception->getMessage(), "results_download_failed");
        }
    }

    public function resultsDownloadUrl($slug): string
    {
        return "https://www.chess.com/tournament/live/" . rawurlencode($slug) . "/download-results";
    }
}
