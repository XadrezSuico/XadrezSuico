<?php
namespace App\Helper;

use Illuminate\Validation\ValidationException;

class SwissManagerPairingsCsv
{
    public static function parse(string $csv): array
    {
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv);
        $lines = preg_split('/\r\n|\n|\r/', $csv);
        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) !== '') $rows[] = str_getcsv($line, ';');
        }
        $required = ['Rodada', 'Mesa', 'ID-B', 'ID-N', 'NoB', 'NoN', 'ResB', 'ResM'];
        if (!$rows) self::invalid('O arquivo de emparceiramentos está vazio.');
        $rows[0] = array_map(function ($value) {
            $value = trim($value);
            return $value === 'ResN' ? 'ResM' : $value;
        }, $rows[0]);
        $missing = array_diff($required, $rows[0]);
        if ($missing) self::invalid('Colunas obrigatórias ausentes: ' . implode(', ', $missing) . '. Exporte os emparceiramentos do Swiss-Manager em CSV separado por ponto e vírgula.');
        $fields = array_flip($rows[0]);
        if (count($rows) < 2) self::invalid('O arquivo não contém emparceiramentos.');
        foreach (array_slice($rows, 1) as $index => $row) {
            foreach ($required as $field) {
                if (!array_key_exists($fields[$field], $row)) self::invalid('Linha ' . ($index + 2) . ' incompleta. Nenhum emparceiramento foi alterado.');
            }
            foreach (['Rodada', 'Mesa'] as $field) {
                if (!ctype_digit(trim($row[$fields[$field]])) || (int) $row[$fields[$field]] < 1) {
                    self::invalid('Linha ' . ($index + 2) . ': rodada ou mesa inválida.');
                }
            }
        }
        return $rows;
    }

    private static function invalid(string $message): void
    {
        throw ValidationException::withMessages(['arquivo' => $message]);
    }
}
