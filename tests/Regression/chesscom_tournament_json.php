<?php

// Run without application dependencies: php tests/Regression/chesscom_tournament_json.php
require_once __DIR__ . '/../../app/Services/Integration/ChessComTournamentJsonValidator.php';

$validator = new App\Services\Integration\ChessComTournamentJsonValidator;
$valid = '{"url":"https://www.chess.com/tournament/live/test-slug","status":"finished","players":[{"username":"Alice"},{"username":"bob"}]}';
$cases = [
    'finished tournament' => [$valid, true],
    'UTF-8 BOM' => ["\xEF\xBB\xBF" . $valid, true],
    'unfinished tournament returned for status check' => [str_replace('finished', 'in_progress', $valid), true],
    'no rounds or groups required' => ['{"status":"finished","players":[{"username":"alice"}]}', true],
    'empty player list' => ['{"status":"finished","players":[]}', true],
    'empty response' => ['', false],
    'HTML challenge' => ['<html>Access denied</html>', false],
    'CSV instead of JSON' => ["Username,Score\nalice,1\n", false],
    'malformed JSON' => ['{"status":', false],
    'null JSON' => ['null', false],
    'array instead of object' => ['[]', false],
    'API error' => ['{"code":0,"message":"Not found"}', false],
    'missing status' => ['{"players":[]}', false],
    'invalid status type' => ['{"status":true,"players":[]}', false],
    'empty status' => ['{"status":" ","players":[]}', false],
    'missing player list' => ['{"status":"finished"}', false],
    'invalid player list' => ['{"status":"finished","players":{}}', false],
    'missing username' => ['{"status":"finished","players":[{}]}', false],
    'invalid username type' => ['{"status":"finished","players":[{"username":42}]}', false],
    'blank username' => ['{"status":"finished","players":[{"username":" "}]}', false],
    'invalid later player' => ['{"status":"finished","players":[{"username":"alice"},{}]}', false],
    'another tournament' => [str_replace('test-slug', 'other-slug', $valid), false],
    'invalid tournament URL' => [str_replace('"https://www.chess.com/tournament/live/test-slug"', '[]', $valid), false],
    'oversized file' => [str_repeat('a', 10 * 1024 * 1024 + 1), false],
];
$failed = 0;
foreach ($cases as $name => $case) {
    list($json, $expected) = $case;
    try {
        $data = $validator->parse($json, 'test-slug');
        $accepted = is_object($data) && is_array($data->players);
    } catch (InvalidArgumentException $exception) {
        $accepted = false;
    }
    if ($accepted !== $expected) {
        fwrite(STDERR, 'FAIL: ' . $name . PHP_EOL);
        $failed++;
    }
}
echo count($cases) . ' tournament JSON checks, ' . $failed . ' failures.' . PHP_EOL;
exit($failed === 0 ? 0 : 1);
