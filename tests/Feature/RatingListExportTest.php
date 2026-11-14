<?php

namespace Tests\Feature;

use App\Cidade;
use App\Clube;
use App\Enxadrista;
use App\Evento;
use App\Http\Controllers\InscricaoGerenciarController;
use App\Inscricao;
use App\Support\SwissManagerExportIdentity;
use App\Torneio;
use Tests\TestCase;

class RatingListExportTest extends TestCase
{
    private function fixture($type, $withClub)
    {
        $player = \Mockery::mock(Enxadrista::class)->makePartial();
        $player->forceFill(['id' => 10, 'name' => 'JOÃO PEDRO DA SILVA', 'firstname' => 'João Pedro',
            'lastname' => 'da Silva', 'fide_name' => 'Silva, Joao Pedro', 'chess_com_username' => 'joao_chess']);
        $player->shouldReceive('hasConfig')->andReturn(false);
        $player->shouldReceive('titles')->andReturn(collect());
        $player->shouldReceive('showRating')->andReturn(1500);
        $player->shouldReceive('getBornToSM')->andReturn('01.01.2000');
        $player->setRelation('pais_nascimento', (object) ['codigo_iso' => 'BRA']);
        $player->setRelation('sexo', (object) ['abbr' => 'M']);
        $city = new Cidade;
        $city->forceFill(['id' => 9, 'ibge_id' => 4104808, 'name' => 'CASCÁVEL']);
        $city->setRelation('estado', (object) ['abbr' => 'PR', 'pais' => (object) ['codigo_iso' => 'BRA']]);
        $club = new Clube;
        $club->forceFill(['id' => 27, 'name' => 'CLUBE SÃO JOSÉ', 'abbr' => 'CSJ']);
        $club->setRelation('cidade', $city);
        $registration = \Mockery::mock(Inscricao::class)->makePartial();
        $registration->shouldReceive('hasConfig')->andReturn(false);
        $registration->forceFill(['id' => 15]);
        $registration->setRelation('enxadrista', $player);
        $registration->setRelation('cidade', $city);
        $registration->setRelation('clube', $withClub ? $club : null);
        $registration->setRelation('categoria', (object) ['cat_code' => 'U18', 'code' => '18']);
        $event = \Mockery::mock(Evento::class)->makePartial();
        $event->forceFill(['exportacao_sm_modelo' => $type, 'usa_fide' => true, 'usa_cbx' => true]);
        $event->setRelation('tipo_rating', null);
        $event->shouldReceive('hasConfig')->andReturn(false);
        $event->shouldReceive('getConfig')->andReturn(null);
        $tournament = \Mockery::mock(Torneio::class)->makePartial();
        $tournament->shouldReceive('getClubsFromRegistrations')->andReturn($withClub ? [$club] : []);
        $tournament->setRelation('inscricoes', collect([$registration]));
        $event->setRelation('torneios', collect([$tournament]));
        return [$registration, $event, $tournament];
    }

    /** @dataProvider exportTypes */
    public function test_rating_list_name_and_club_match_txt($type, $withClub)
    {
        list($registration, $event, $tournament) = $this->fixture($type, $withClub);
        $method = new \ReflectionMethod(InscricaoGerenciarController::class, 'generateTxt');
        $method->setAccessible(true);
        $txt = $method->invoke(new InscricaoGerenciarController, [$registration], $event, $tournament);
        $lines = explode("\r\n", trim($txt));
        $row = array_combine(explode(';', $lines[0]), explode(';', $lines[1]));
        $identity = SwissManagerExportIdentity::forRegistration($registration, $type);
        $this->assertSame($row['Sobrenome'].','.$row['Nome'], $identity['name']);
        $this->assertSame($row['NoClube'], (string) $identity['club_number']);
        $this->assertSame($row['Nome Clube'], $identity['club_name']);

        $html = view('exports.sm.inscritos', ['evento' => $event])->render();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $cells = (new \DOMXPath($dom))->query('//tbody/tr/td');
        $this->assertSame($identity['name'], trim($cells->item(2)->textContent));
        $this->assertSame((string) $identity['club_number'], trim($cells->item(4)->textContent));
        $this->assertSame($identity['club_name'], trim($cells->item(5)->textContent));
    }

    public function test_fide_name_fallback_and_abbreviation_match_the_txt_formatter()
    {
        list($registration) = $this->fixture(1, true);
        $registration->enxadrista->fide_name = null;
        $identity = SwissManagerExportIdentity::forRegistration($registration, 1);
        $this->assertSame('Da Silva,Joao Pedro', $identity['name']);
        $registration->enxadrista->fide_name = 'Silva, Joao Pedro Antonio Carlos Eduardo';
        $identity = SwissManagerExportIdentity::forRegistration($registration, 7);
        $this->assertSame($registration->enxadrista->getSwissManagerFideName()['full'], $identity['name']);
        $this->assertSame('Silva,Joao P A C E', $identity['name']);
    }

    public function test_missing_split_names_are_derived_without_saving_the_player()
    {
        list($registration) = $this->fixture(3, false);
        $registration->enxadrista->firstname = null;
        $registration->enxadrista->lastname = null;
        $registration->enxadrista->shouldNotReceive('save');
        $identity = SwissManagerExportIdentity::forRegistration($registration, 3);
        $this->assertSame('Joao Pedro ,Da Silva', $identity['name']);
    }

    public function exportTypes()
    {
        $cases = [];
        foreach ([0, 1, 2, 3, 4, 5, 6, 7, null, 99] as $type) {
            $cases[] = [$type, true];
            // The existing team TXT assumes a club when assigning the team number.
            if ($type !== 6) $cases[] = [$type, false];
        }
        return $cases;
    }

    public function test_team_rating_list_without_club_has_empty_club_and_zero_number()
    {
        list($registration) = $this->fixture(6, false);
        $identity = SwissManagerExportIdentity::forRegistration($registration, 6);
        $this->assertSame(0, $identity['club_number']);
        $this->assertSame('', $identity['club_name']);
    }
}
