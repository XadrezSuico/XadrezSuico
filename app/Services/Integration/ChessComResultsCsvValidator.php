<?php

namespace App\Services\Integration;

use InvalidArgumentException;

class ChessComResultsCsvValidator
{
    public function validate(string $csv): void
    {
        if (strlen($csv) > 10 * 1024 * 1024 || trim($csv) === '' || strpos($csv, "\0") !== false) {
            throw new InvalidArgumentException('Arquivo vazio, muito grande ou inválido.');
        }

        $stream = fopen('php://temp', 'r+');
        try {
            fwrite($stream, preg_replace('/^\xEF\xBB\xBF/', '', $csv));
            rewind($stream);
            $header = fgetcsv($stream, 0, ',', '"', '\\');
            if ($header === false) {
                throw new InvalidArgumentException('Cabeçalho CSV ausente.');
            }
            $header = array_map(function ($value) {
                return strtolower(trim($value));
            }, $header);
            $username = array_search('username', $header, true);
            $score = array_search('score', $header, true);
            if ($username === false || $score === false || count(array_unique($header)) !== count($header)) {
                throw new InvalidArgumentException('O CSV precisa conter as colunas Username e Score, sem cabeçalhos duplicados.');
            }

            $rows = 0;
            while (($row = fgetcsv($stream, 0, ',', '"', '\\')) !== false) {
                if ($row === [null]) {
                    continue;
                }
                if (count($row) !== count($header) || trim($row[$username]) === '' || !is_numeric($row[$score])) {
                    throw new InvalidArgumentException('O CSV contém uma linha de resultados inválida.');
                }
                $rows++;
            }
            if ($rows === 0) {
                throw new InvalidArgumentException('O CSV não contém resultados.');
            }
        } finally {
            fclose($stream);
        }
    }
}
