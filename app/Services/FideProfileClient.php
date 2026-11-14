<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FideProfileClient
{
    private $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        $this->client = $client ?: HttpClient::create();
    }

    public function fetch($fideId): string
    {
        if (!preg_match('/^[1-9][0-9]{0,11}$/D', (string) $fideId)) {
            throw new \InvalidArgumentException('Código FIDE inválido.');
        }
        $url = 'https://ratings.fide.com/profile/' . $fideId;
        $options = [
            'headers' => ['User-Agent' => 'Mozilla/5.0 (compatible; XadrezSuico/1.0)'],
            'timeout' => (float) config('services.fide.timeout', 8),
            'max_duration' => (float) config('services.fide.max_duration', 25),
            'max_redirects' => 0,
        ];
        if (config('services.fide.proxy_enabled')) {
            $host = (string) config('services.fide.proxy_host');
            $port = filter_var(config('services.fide.proxy_port'), FILTER_VALIDATE_INT);
            $token = (string) config('services.fide.proxy_token');
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9.-]*$/D', $host)
                || !$port || $port < 1024 || $port > 65535 || strlen($token) < 32) {
                throw new \RuntimeException('Configuração do proxy FIDE inválida.');
            }
            $url = 'https://' . $host . ':' . $port . '/profile/' . $fideId;
            $options['headers']['Authorization'] = 'Bearer ' . $token;
            if (config('services.fide.proxy_ca')) {
                $options['cafile'] = config('services.fide.proxy_ca');
            }
        }
        $response = $this->client->request('GET', $url, $options);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Consulta FIDE indisponível (HTTP ' . $response->getStatusCode() . ').');
        }
        return $response->getContent();
    }
}
