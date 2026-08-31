<?php

namespace App\Services;

use App\Enxadrista;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class CBXAnuidadeService
{
    public function consultarEnxadrista(Enxadrista $enxadrista): array
    {
        if ($enxadrista->hasConfig('united_to')) {
            return $this->resultadoErro('Cadastro unificado — consulta CBX não permitida.');
        }

        $cbxId = trim((string) $enxadrista->cbx_id);
        if (!(intval($cbxId) > 0)) {
            return $this->resultadoSemId();
        }

        return $this->consultarPorCbxId($cbxId);
    }

    public function consultarPorCbxId(string $cbxId): array
    {
        $cbxId = trim($cbxId);
        if (!(intval($cbxId) > 0)) {
            return $this->resultadoSemId();
        }

        try {
            $client = new Client([
                'http_errors' => false,
                'timeout' => 30,
            ]);

            $url = 'https://cbx.org.br/buscar-jogadores/?nm=&id=' . urlencode($cbxId);
            $response = $client->get($url);

            if ($response->getStatusCode() !== 200) {
                Log::debug("CBXAnuidadeService::consultarPorCbxId - HTTP {$response->getStatusCode()} para ID {$cbxId}");

                return $this->resultadoErro('Não foi possível consultar a CBX.');
            }

            $html = (string) $response->getBody();
            $dataPagto = $this->extrairDataPagto($html, $cbxId);

            if ($dataPagto === null) {
                return $this->resultadoErro('Jogador não encontrado na CBX.');
            }

            return $this->resolveStatus($dataPagto);
        } catch (\Throwable $e) {
            Log::debug("CBXAnuidadeService::consultarPorCbxId - exceção para ID {$cbxId}: " . $e->getMessage());

            return $this->resultadoErro('Erro ao consultar a CBX.');
        }
    }

    public function resolveStatus(string $dataPagto): array
    {
        $dataPagto = trim($dataPagto);

        if (strcasecmp($dataPagto, 'Pendente') === 0) {
            return [
                'status' => 'pendente',
                'label' => 'Pendente',
                'data_pagto' => $dataPagto,
                'detalhe' => 'Anuidade pendente na CBX.',
            ];
        }

        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $dataPagto, $matches)) {
            $ano = (int) $matches[3];
            $anoAtual = (int) date('Y');

            if ($ano === $anoAtual) {
                return [
                    'status' => 'pago',
                    'label' => 'Pago',
                    'data_pagto' => $dataPagto,
                    'detalhe' => "Pagamento registrado em {$dataPagto}.",
                ];
            }

            return [
                'status' => 'pendente',
                'label' => 'Pendente',
                'data_pagto' => $dataPagto,
                'detalhe' => "Último pagamento em {$dataPagto} — anuidade do ano atual pendente.",
            ];
        }

        return $this->resultadoErro("Resposta inesperada da CBX: {$dataPagto}");
    }

    private function extrairDataPagto(string $html, string $cbxId): ?string
    {
        $crawler = new Crawler($html);
        $dataPagto = null;

        $crawler->filter('table tr')->each(function (Crawler $row) use ($cbxId, &$dataPagto) {
            if ($dataPagto !== null) {
                return;
            }

            $cells = $row->filter('td');
            if ($cells->count() < 5) {
                return;
            }

            $idCbx = trim($cells->eq(0)->text());
            if ($idCbx !== $cbxId) {
                return;
            }

            $dataPagto = trim($cells->eq(4)->text());
        });

        return $dataPagto;
    }

    private function resultadoSemId(): array
    {
        return [
            'status' => 'sem_id',
            'label' => 'Sem ID CBX',
            'data_pagto' => '-',
            'detalhe' => 'Enxadrista sem ID CBX informado.',
        ];
    }

    private function resultadoErro(string $detalhe): array
    {
        return [
            'status' => 'erro',
            'label' => 'Erro',
            'data_pagto' => '-',
            'detalhe' => $detalhe,
        ];
    }
}
