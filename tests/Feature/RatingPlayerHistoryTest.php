<?php
namespace Tests\Feature;

use App\Enxadrista;
use App\Rating;
use App\MovimentacaoRating;
use Tests\TestCase;

class RatingPlayerHistoryTest extends TestCase
{
    public function test_history_preserves_movements_when_tournament_or_event_was_removed()
    {
        $player = \Mockery::mock(Enxadrista::class)->makePartial();
        $player->name = 'Jogador de teste';
        $player->setRelation('cidade', null)->setRelation('clube', null);
        $player->shouldReceive('getNomePublico')->andReturn('Jogador de teste');
        $player->shouldReceive('getNascimentoPublico')->andReturn('01/01/2000');
        $initial = new MovimentacaoRating;
        $initial->forceFill(['id'=>1,'is_inicial'=>true,'valor'=>1800,'created_at'=>'2026-01-10']);
        $initial->setRelation('torneio', null);
        $orphan = new MovimentacaoRating;
        $orphan->forceFill(['id'=>2,'is_inicial'=>false,'valor'=>15,'created_at'=>'2026-11-17']);
        $orphan->setRelation('torneio', null);
        $tournament = new \App\Torneio;
        $tournament->name = 'Torneio preservado';
        $tournament->setRelation('evento', null);
        $noEvent = new MovimentacaoRating;
        $noEvent->forceFill(['id'=>3,'is_inicial'=>false,'valor'=>-5,'created_at'=>'2026-11-18']);
        $noEvent->setRelation('torneio', $tournament);
        $rating = \Mockery::mock(Rating::class)->makePartial();
        $rating->valor = 1810;
        $rating->shouldReceive('getMovimentacoes')->andReturn([$initial,$orphan,$noEvent]);
        // Render the actual page without the AdminLTE navigation/database dependencies.
        $source = file_get_contents(resource_path('views/rating/view.blade.php'));
        $source = str_replace("@extends('adminlte::page')", '', $source);
        $source .= "\n@yield('content')\n@yield('js')";
        $compiled = app('blade.compiler')->compileString($source);
        $temporary = tempnam(sys_get_temp_dir(), 'rating-view-');
        unlink($temporary);
        $path = $temporary . '.php';
        file_put_contents($path, $compiled);
        try {
            $html = app('view')->file($path, ['tipo_rating'=>(object)['id'=>1,'name'=>'Teste'], 'enxadrista'=>$player,'rating'=>$rating])->render();
        } finally { unlink($path); }
        $this->assertStringContainsString('Não disponível', $html);
        $this->assertStringContainsString('Torneio preservado', $html);
        $this->assertStringContainsString('rating: 1815', $html);
        $this->assertStringContainsString('rating: 1810', $html);
        $this->assertStringContainsString('2026-11-17', $html);
    }
}
