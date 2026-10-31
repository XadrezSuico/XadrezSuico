<?php

namespace App\Services\Integration;

use InvalidArgumentException;

class ChessComTournamentJsonValidator
{
    public function parse(string $json, string $slug)
    {
        if (strlen($json) > 10 * 1024 * 1024) {
            throw new InvalidArgumentException('O arquivo deve ter até 10 MB.');
        }
        $data = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $json));
        if (json_last_error() !== JSON_ERROR_NONE || !is_object($data) ||
            !isset($data->status, $data->players) || !is_string($data->status) ||
            trim($data->status) === '' || !is_array($data->players)) {
            throw new InvalidArgumentException('Envie o arquivo JSON do torneio obtido pelo link do Chess.com.');
        }
        if (isset($data->url)) {
            $path = is_string($data->url) ? parse_url($data->url, PHP_URL_PATH) : false;
            if (!is_string($path) || rawurldecode(basename(rtrim($path, '/'))) !== $slug) {
                throw new InvalidArgumentException('O arquivo pertence a outro torneio. Baixe o JSON pelo link indicado.');
            }
        }
        foreach ($data->players as $player) {
            if (!is_object($player) || !isset($player->username) || !is_string($player->username) || trim($player->username) === '') {
                throw new InvalidArgumentException('O JSON contém uma inscrição inválida.');
            }
        }
        return $data;
    }
}
