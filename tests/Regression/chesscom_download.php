<?php
// Standalone download contract checks with HTTP and framework test doubles.
// Run: php tests/Regression/chesscom_download.php
namespace App\Http\Controllers { class Controller {} }
namespace GuzzleHttp\Exception { class TransferException extends \Exception {} }
namespace GuzzleHttp {
    class Client {
        public static $status = 200;
        public static $fail = false;
        public static $body = "Username,Score\nalice,1\n";
        public static $requests = 0;
        public static $options;
        public static $url;
        public function __construct($options) { self::$options = $options; }
        public function get($url) {
            self::$url = $url;
            self::$requests++;
            if (self::$fail) { throw new \GuzzleHttp\Exception\TransferException('Timeout'); }
            return new class {
                public function getStatusCode() { return Client::$status; }
                public function getBody() { return Client::$status === 200 ? Client::$body : '<html>Forbidden</html>'; }
            };
        }
    }
}
namespace {
    function env($key, $default = null) { return 'production'; }
    require __DIR__ . '/../../app/Services/Integration/ChessComTournamentJsonValidator.php';
    require __DIR__ . '/../../app/Http/Controllers/Integration/Online/ChessComIntegrationController.php';
    $controller = new App\Http\Controllers\Integration\Online\ChessComIntegrationController;
    $checks = 0;
    function check($condition) {
        global $checks;
        if (!$condition) { throw new \RuntimeException('Failed check ' . ($checks + 1)); }
        $checks++;
    }
    $result = $controller->getResults('test-slug');
    check($result['ok'] === 1 && $result['data'] === "Username,Score\nalice,1\n");
    check(GuzzleHttp\Client::$url === 'https://www.chess.com/tournament/live/test-slug/download-results');
    check(GuzzleHttp\Client::$options['timeout'] === 30 && GuzzleHttp\Client::$options['connect_timeout'] === 10);
    check(GuzzleHttp\Client::$options['verify'] === true && GuzzleHttp\Client::$options['http_errors'] === false);
    GuzzleHttp\Client::$status = 403;
    $result = $controller->getResults('test-slug');
    check($result['ok'] === 0 && $result['status_code'] === 403 && $result['message_key'] === 'results_download_failed');
    check(strpos($result['response_body'], 'Forbidden') !== false);
    GuzzleHttp\Client::$fail = true;
    $result = $controller->getResults('test-slug');
    check($result['ok'] === 0 && $result['status_code'] === 0 && $result['response_body'] === 'Timeout');
    check($controller->resultsDownloadUrl('test/slug?') === 'https://www.chess.com/tournament/live/test%2Fslug%3F/download-results');
    GuzzleHttp\Client::$fail = false;
    GuzzleHttp\Client::$status = 200;
    GuzzleHttp\Client::$requests = 0;
    GuzzleHttp\Client::$body = '{"status":"finished","players":[{"username":"alice"}],"rounds":["https://api.chess.com/round"]}';
    $result = $controller->getTournament('test-slug');
    check($result['ok'] === 1 && json_decode($result['data'])->players[0]->username === 'alice');
    check(GuzzleHttp\Client::$url === 'https://api.chess.com/pub/tournament/test-slug');
    check(GuzzleHttp\Client::$requests === 1);
    check(GuzzleHttp\Client::$options['timeout'] === 30 && GuzzleHttp\Client::$options['connect_timeout'] === 10);
    GuzzleHttp\Client::$body = '{"status":"in_progress","players":[]}';
    $result = $controller->getTournament('test-slug');
    check($result['ok'] === 1 && json_decode($result['data'])->status === 'in_progress');
    GuzzleHttp\Client::$body = '<html>Forbidden</html>';
    $result = $controller->getTournament('test-slug');
    check($result['ok'] === 0 && $result['message_key'] === 'invalid_tournament_json');
    GuzzleHttp\Client::$body = '{"status":"finished","players":[{}]}';
    $result = $controller->getTournament('test-slug');
    check($result['ok'] === 0 && $result['message_key'] === 'invalid_tournament_json');
    GuzzleHttp\Client::$status = 403;
    $result = $controller->getTournament('test-slug');
    check($result['ok'] === 0 && $result['status_code'] === 403 && $result['message_key'] === 'players_download_failed');
    GuzzleHttp\Client::$fail = true;
    $result = $controller->getTournament('test-slug');
    check($result['ok'] === 0 && $result['status_code'] === 0 && $result['message_key'] === 'players_download_failed');
    check($controller->tournamentDownloadUrl('test/slug?') === 'https://api.chess.com/pub/tournament/test%2Fslug%3F');
    echo $checks . " HTTP simulation checks, 0 failures.\n";
}
