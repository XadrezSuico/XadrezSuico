<?php

namespace App\Console\Commands;

use App\Services\FideProfileClient;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class FideConnectionCheck extends Command
{
    protected $signature = 'fide:check {id=1503014 : Código de um perfil FIDE existente}';
    protected $description = 'Verifica conexão e HTML FIDE sem alterar enxadristas ou ratings';

    public function handle(FideProfileClient $client)
    {
        $start = microtime(true);
        try {
            $html = $client->fetch($this->argument('id'));
            if ((new Crawler($html))->filter('h1.player-title')->count() === 0) {
                throw new \RuntimeException('HTML sem perfil FIDE válido.');
            }
            $this->info(sprintf('FIDE OK via %s em %.2fs; HTML válido; nenhum dado alterado.',
                config('services.fide.proxy_enabled') ? 'proxy' : 'conexão direta', microtime(true) - $start));
            return 0;
        } catch (\Exception $e) {
            // Transport exceptions can contain internal URLs; do not print credentials/config.
            $this->error('Falha na conexão ou no HTML FIDE. Nenhum dado alterado.');
            return 1;
        }
    }
}
