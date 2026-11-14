<?php

namespace Tests\Feature;

use App\Enxadrista;
use App\Helper\FideFederationHelper;
use App\Http\Controllers\CBXRatingController;
use App\Http\Controllers\FIDERatingController;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class FideRatingImportTest extends TestCase
{
    private function invoke($method, ...$args)
    {
        $reflection = new \ReflectionMethod(FIDERatingController::class, $method);
        $reflection->setAccessible(true);
        return $reflection->invoke(null, ...$args);
    }

    public function test_current_profile_extracts_name_and_each_rating_modality()
    {
        $html = '<h1 class="player-title">Silva, Joao</h1>'
            .'<div class="profile-standart profile-game"><p>1987</p></div>'
            .'<div class="profile-rapid profile-game"><p>2012</p></div>'
            .'<div class="profile-blitz profile-game"><p>1940</p></div>';
        $crawler = new Crawler($html);
        $this->assertFalse($this->invoke('isPlayerNotFound', $crawler, $html));
        $this->assertSame('Silva, Joao', $this->invoke('extractName', $crawler));
        $this->assertSame(['standard' => 1987, 'rapid' => 2012, 'blitz' => 1940], $this->invoke('extractRatings', $crawler));
    }

    public function test_unrated_and_missing_modalities_return_null()
    {
        $crawler = new Crawler('<div class="profile-standart profile-game"><p>Not rated</p></div>'
            .'<div class="profile-rapid profile-game"><p> </p></div>');
        $this->assertSame(['standard' => null, 'rapid' => null, 'blitz' => null], $this->invoke('extractRatings', $crawler));
        $this->assertTrue($this->invoke('isPlayerNotFound', new Crawler('<p>No record found</p>'), 'No record found'));
        $this->assertTrue($this->invoke('isPlayerNotFound', new Crawler('<p>Unknown profile</p>'), 'Unknown profile'));
    }

    public function test_federation_uses_flag_and_falls_back_to_name()
    {
        $crawler = new Crawler('<div class="profile-info-country"><img src="/flags/br.svg">Brazil</div>');
        $this->assertSame('BRA', $this->invoke('extractFederationCode', $crawler, 'Brazil'));
        $this->assertSame('GER', FideFederationHelper::resolve(null, 'Germany'));
        $this->assertSame('POR', FideFederationHelper::resolve('pt', 'Portugal'));
        $this->assertNull(FideFederationHelper::resolve(null, 'Unknown federation'));
    }

    public function test_rating_updates_preserve_modality_mapping_and_remove_unrated_values()
    {
        $player = \Mockery::mock(Enxadrista::class)->makePartial();
        $player->shouldReceive('setRating')->once()->with(0, 0, 1987);
        $player->shouldReceive('deleteRating')->once()->with(0, 1);
        $player->shouldReceive('setRating')->once()->with(0, 2, 1940);
        $this->invoke('applyRatings', $player, 0, ['standard'=>1987, 'rapid'=>null, 'blitz'=>1940], true);
        $this->assertTrue(true); // Mockery checks the writes and their arguments at teardown.
    }

    public function test_preview_does_not_persist_ratings_or_missing_profile()
    {
        $player = \Mockery::mock(Enxadrista::class)->makePartial();
        $player->shouldNotReceive('setRating');
        $player->shouldNotReceive('deleteRating');
        $player->shouldNotReceive('save');
        $this->invoke('applyRatings', $player, 0, ['standard'=>1987, 'rapid'=>2012, 'blitz'=>1940], false);
        $player->fide_name = 'Old name';
        $player->fide_federation = 'BRA';
        $this->invoke('markNotFound', $player, 0, false);
        $this->assertFalse($player->encontrado_fide);
        $this->assertNull($player->fide_name);
        $this->assertNull($player->fide_federation);
    }

    public function test_federation_migration_preserves_existing_players_and_can_roll_back()
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        \Illuminate\Support\Facades\DB::purge('sqlite');
        \Illuminate\Support\Facades\Schema::create('enxadrista', function ($table) {
            $table->increments('id');
            $table->string('fide_name')->nullable();
        });
        \Illuminate\Support\Facades\DB::table('enxadrista')->insert(['id'=>1, 'fide_name'=>'Silva, Joao']);
        require_once base_path('database/migrations/2026_09_03_120000_update_enxadrista_table_add_fide_federation.php');
        $migration = new \UpdateEnxadristaTableAddFideFederation;
        $migration->up();
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('enxadrista', 'fide_federation'));
        $this->assertNull(\Illuminate\Support\Facades\DB::table('enxadrista')->value('fide_federation'));
        $this->assertSame('Silva, Joao', \Illuminate\Support\Facades\DB::table('enxadrista')->value('fide_name'));
        $migration->down();
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('enxadrista', 'fide_federation'));
        $this->assertSame(1, \Illuminate\Support\Facades\DB::table('enxadrista')->count());
    }

    public function test_cbx_keeps_existing_fide_id_and_ignores_missing_id()
    {
        $player = new Enxadrista;
        $player->fide_id = '12345';
        $this->assertFalse(CBXRatingController::importFideIdFromCbxIfMissing($player, 'ID FIDE: <strong>54321</strong>'));
        $this->assertSame('12345', $player->fide_id);
        $player->fide_id = null;
        $this->assertFalse(CBXRatingController::importFideIdFromCbxIfMissing($player, '<p>Sem ID FIDE</p>'));
    }
}
