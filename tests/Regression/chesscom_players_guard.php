<?php
// Verify that invalid or unfinished tournaments never reset registration flags.
// Run: php tests/Regression/chesscom_players_guard.php
namespace App\Http\Controllers { class Controller {} }
namespace App {
    class Evento { public $id = 1; }
    class Torneio {
        public $id = 2;
        public $mutations = 0;
        public function getConfig($key, $raw) { return 'test-slug'; }
        public function chesscom_setAllRegistrationsNotFound() {
            $this->mutations++;
            throw new \RuntimeException('Registration reset must not be reached');
        }
        public function chesscom_clearLastApiError() {
            $this->mutations++;
            throw new \RuntimeException('Error cleanup must not be reached');
        }
    }
}
namespace Illuminate\Support {
    class MessageBag {
        public $messages = [];
        public function add($key, $value) { $this->messages[$key] = $value; }
    }
}
namespace Illuminate\Support\Facades {
    class DB {
        public static $transactions = 0;
        public static function transaction($callback) { self::$transactions++; return $callback(); }
    }
}
namespace {
    class GuardRedirect {
        public $url;
        public $errors;
        public function __construct($url) { $this->url = $url; }
        public function withErrors($errors) { $this->errors = $errors; return $this; }
    }
    function url($path) { return $path; }
    function redirect($path) { return new GuardRedirect($path); }
    function config($key) { return 'pt-br'; }
    function trans($key, $replace = [], $locale = null) { return $key; }
    require __DIR__ . '/../../app/Services/Integration/ChessComTournamentJsonValidator.php';
    require __DIR__ . '/../../app/Http/Controllers/TorneioController.php';
    $reflection = new ReflectionClass(App\Http\Controllers\TorneioController::class);
    $controller = $reflection->newInstanceWithoutConstructor();
    $method = $reflection->getMethod('chesscomCheckPlayers');
    // PHP 7 needs explicit access to private methods; PHP 8.5 makes this automatic.
    if (PHP_VERSION_ID < 80100) { $method->setAccessible(true); }
    $event = new App\Evento;
    $tournament = new App\Torneio;
    $cases = [
        'unfinished' => ['{"status":"in_progress","players":[{"username":"alice"}]}', false],
        'registration open' => ['{"status":"registration","players":[]}', false],
        'HTML' => ['<html>Forbidden</html>', true],
        'invalid later player' => ['{"status":"finished","players":[{"username":"alice"},{}]}', true],
        'wrong tournament' => ['{"url":"https://www.chess.com/tournament/live/other","status":"finished","players":[]}', true],
    ];
    foreach ($cases as $name => $case) {
        $response = $method->invoke($controller, $event, $tournament, $case[0]);
        $expectedUrl = $case[1] ? '/evento/1/torneios/2/chesscom/players/file' : '/evento/dashboard/1/?tab=torneio';
        if ($response->url !== $expectedUrl || $tournament->mutations !== 0 || Illuminate\Support\Facades\DB::$transactions !== 0) {
            throw new RuntimeException('FAIL: ' . $name);
        }
        if ($case[1] && !isset($response->errors['arquivo'])) {
            throw new RuntimeException('Missing file validation error: ' . $name);
        }
        if (!$case[1] && $response->errors->messages['type'] !== 'warning') {
            throw new RuntimeException('Missing unfinished-tournament warning: ' . $name);
        }
    }
    echo count($cases) . " registration guard checks, 0 failures.\n";
}
