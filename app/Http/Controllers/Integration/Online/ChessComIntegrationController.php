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

    public function tournamentDownloadUrl($slug): string
    {
        return "https://api.chess.com/pub/tournament/" . rawurlencode($slug);
    }

    public function getTournament($slug){
        $client = new Client([
            'http_errors' => false,
            'verify' => env("APP_ENV", "local") != "local",
            'connect_timeout' => 10,
            'timeout' => 30,
        ]);
        $requestUrl = $this->tournamentDownloadUrl($slug);
        try {
            $response = $client->get($requestUrl);
            $json = (string) $response->getBody();
            if ($response->getStatusCode() != 200) {
                return $this->apiErrorResponse($requestUrl, $slug, $response->getStatusCode(), $json, "players_download_failed");
            }
            try {
                (new \App\Services\Integration\ChessComTournamentJsonValidator)->parse($json, $slug);
            } catch (\InvalidArgumentException $exception) {
                return $this->apiErrorResponse($requestUrl, $slug, 200, $json, "invalid_tournament_json");
            }
            return ["ok" => 1, "error" => 0, "data" => $json];
        } catch (\GuzzleHttp\Exception\TransferException $exception) {
            return $this->apiErrorResponse($requestUrl, $slug, 0, $exception->getMessage(), "players_download_failed");
        }
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
