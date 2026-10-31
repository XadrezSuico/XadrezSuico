<?php

// Run without application dependencies: php tests/Regression/chesscom_csv.php
require_once __DIR__ . '/../../app/Services/Integration/ChessComResultsCsvValidator.php';

$validator = new App\Services\Integration\ChessComResultsCsvValidator;
$cases = [
    'results with tiebreaks' => ["Rank,Username,Score,Tiebreak\n1,alice,3.5,10\n2,bob,2,8\n", true],
    'UTF-8 BOM and Windows line endings' => ["\xEF\xBB\xBFRank,Username,Score\r\n1,alice,1\r\n", true],
    'quoted CSV fields' => ["Username,Score,Name\n\"alice\",0,\"Alice, Example\"\n", true],
    'blank lines' => ["Username,Score\n\nalice,1\n\n", true],
    'empty response' => ['', false],
    'HTML challenge' => ['<!DOCTYPE html><html><body>Access denied</body></html>', false],
    'HTML with successful HTTP status' => ["<html>\nUsername,Score\nalice,1\n</html>", false],
    'JSON response' => ['{"error":"forbidden"}', false],
    'missing username' => ["Rank,Score\n1,3\n", false],
    'missing score' => ["Username,Rank\nalice,1\n", false],
    'header only' => ["Username,Score\n", false],
    'duplicate header' => ["Username,Score,Score\nalice,1,2\n", false],
    'invalid score' => ["Username,Score\nalice,invalid\n", false],
    'empty username' => ["Username,Score\n,1\n", false],
    'truncated row' => ["Username,Score\nalice\n", false],
    'invalid later row' => ["Username,Score\nalice,1\nbob,invalid\n", false],
    'binary file' => ["Username,Score\nalice,1\0\n", false],
    'oversized file' => [str_repeat('a', 10 * 1024 * 1024 + 1), false],
];
$failed = 0;
foreach ($cases as $name => $case) {
    list($csv, $expected) = $case;
    try {
        $validator->validate($csv);
        $accepted = true;
    } catch (InvalidArgumentException $exception) {
        $accepted = false;
    }
    if ($accepted !== $expected) {
        fwrite(STDERR, "FAIL: " . $name . PHP_EOL);
        $failed++;
    }
}
echo count($cases) . ' CSV checks, ' . $failed . ' failures.' . PHP_EOL;
exit($failed === 0 ? 0 : 1);
