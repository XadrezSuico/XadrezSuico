<?php

namespace Tests\Feature;

use App\Enxadrista;
use App\Http\Controllers\FIDERatingController;
use App\Services\FideProfileClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\TestCase;

class FideProxyTest extends TestCase
{
    public function test_proxy_uses_configured_https_address_token_and_certificate()
    {
        config(['services.fide.proxy_enabled' => true, 'services.fide.proxy_host' => 'fide.internal',
            'services.fide.proxy_port' => 8443, 'services.fide.proxy_token' => str_repeat('x', 40),
            'services.fide.proxy_ca' => '/tmp/proxy.crt']);
        $http = new MockHttpClient(function ($method, $url, $options) {
            $this->assertSame('GET', $method);
            $this->assertSame('https://fide.internal:8443/profile/123', $url);
            $this->assertSame('/tmp/proxy.crt', $options['cafile']);
            $this->assertSame(0, $options['max_redirects']);
            $this->assertStringContainsString('Bearer ' . str_repeat('x', 40), implode(' ', $options['headers']));
            return new MockResponse('<h1 class="player-title">Test</h1>');
        });
        $this->assertStringContainsString('player-title', (new FideProfileClient($http))->fetch(123));
    }

    public function test_disabled_proxy_uses_fide_directly()
    {
        config(['services.fide.proxy_enabled' => false]);
        $http = new MockHttpClient(function ($method, $url, $options) {
            $this->assertSame('https://ratings.fide.com/profile/123', $url);
            $this->assertArrayNotHasKey('authorization', $options['normalized_headers']);
            return new MockResponse('html');
        });
        $this->assertSame('html', (new FideProfileClient($http))->fetch(123));
    }

    public function test_proxy_failure_does_not_fall_back_to_blocked_direct_connection()
    {
        config(['services.fide.proxy_enabled' => true, 'services.fide.proxy_host' => 'fide.internal',
            'services.fide.proxy_port' => 8443, 'services.fide.proxy_token' => str_repeat('x', 40)]);
        $http = new MockHttpClient([new MockResponse('Unavailable', ['http_code' => 502])]);
        try {
            (new FideProfileClient($http))->fetch(123);
            $this->fail('Expected upstream failure');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('502', $e->getMessage());
        }
        $this->assertSame(1, $http->getRequestsCount());
    }

    public function test_network_errors_and_unexpected_html_preserve_existing_data()
    {
        foreach ([null, '<html>Access denied</html>'] as $html) {
            $client = \Mockery::mock(FideProfileClient::class);
            if ($html === null) {
                $client->shouldReceive('fetch')->once()->andThrow(new \RuntimeException('timeout'));
            } else {
                $client->shouldReceive('fetch')->once()->andReturn($html);
            }
            $this->app->instance(FideProfileClient::class, $client);
            $player = \Mockery::mock(Enxadrista::class)->makePartial();
            $player->fide_id = 123;
            $player->fide_name = 'Existing name';
            $player->fide_federation = 'BRA';
            $player->encontrado_fide = true;
            $player->fide_last_update = '2026-10-01 00:00:00';
            $player->shouldNotReceive('save');
            $player->shouldNotReceive('deleteRating');
            $player->shouldNotReceive('setRating');
            $this->assertSame($player, FIDERatingController::getRating($player, false, true));
            $this->assertSame('Existing name', $player->fide_name);
            $this->assertSame('BRA', $player->fide_federation);
            $this->assertTrue($player->encontrado_fide);
            $this->assertSame('2026-10-01 00:00:00', $player->fide_last_update);
        }
    }

    public function test_diagnostic_command_checks_html_without_database_writes()
    {
        $client = \Mockery::mock(FideProfileClient::class);
        $client->shouldReceive('fetch')->with('123')->once()->andReturn('<h1 class="player-title">Test</h1>');
        $this->app->instance(FideProfileClient::class, $client);
        $this->artisan('fide:check', ['id' => '123'])->assertExitCode(0);
    }

    public function test_overlapping_batch_exits_before_querying_players()
    {
        $originalStorage = storage_path();
        $testStorage = sys_get_temp_dir() . '/fide-batch-test-' . uniqid();
        mkdir($testStorage . '/framework/cache', 0700, true);
        $this->app->useStoragePath($testStorage);
        $lock = fopen(storage_path('framework/cache/fide-rating-update.lock'), 'c');
        $this->assertTrue(flock($lock, LOCK_EX | LOCK_NB));
        try {
            ob_start();
            (new FIDERatingController)->updateRatings();
            $message = ob_get_clean();
            $this->assertStringContainsString('Atualização FIDE já em andamento', $message);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
            $this->app->useStoragePath($originalStorage);
        }
    }

    public function test_invalid_player_id_cannot_inject_a_proxy_path()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new FideProfileClient(new MockHttpClient()))->fetch('../health');
    }
}
