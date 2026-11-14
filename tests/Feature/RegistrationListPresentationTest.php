<?php

namespace Tests\Feature;

use App\Evento;
use App\Inscricao;
use App\Torneio;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RegistrationListPresentationTest extends TestCase
{
    private function fixture($allowed = true)
    {
        Auth::shouldReceive('user')->andReturn(new class($allowed) {
            private $allowed;
            public function __construct($allowed) { $this->allowed = $allowed; }
            public function hasPermissionGlobal() { return $this->allowed; }
            public function hasPermissionGlobalbyPerfil($profiles) { return $this->allowed; }
            public function hasPermissionEventByPerfil($id, $profiles) { return false; }
            public function hasPermissionGroupEventByPerfil($id, $profiles) { return false; }
        });
        $event = \Mockery::mock(Evento::class)->makePartial();
        $event->forceFill(['id'=>71,'name'=>'Evento de teste','usa_fide'=>true,'usa_cbx'=>true]);
        $event->setRelation('tipo_rating', null)->setRelation('classificador', null)->setRelation('classifica', null);
        $event->setRelation('grupo_evento', (object) ['id'=>1]);
        $event->shouldReceive('isPaid')->andReturn(true);
        $event->shouldReceive('hasConfig')->andReturn(false);
        $event->shouldReceive('getConfig')->andReturn(null);
        $event->shouldReceive('event_classificators')->andReturn(collect());
        $event->shouldReceive('campos')->andReturn(collect([(object)['id'=>1,'name'=>'Campo adicional']]));
        $registration = \Mockery::mock(Inscricao::class)->makePartial();
        $registration->forceFill(['id'=>6039,'torneio_id'=>2,'paid'=>true,'confirmado'=>true]);
        $registration->shouldReceive('getPaymentInfo')->andReturn('payment-id');
        $registration->shouldReceive('isDeletavel')->andReturn(false);
        $registration->shouldReceive('isPresent')->andReturn(true);
        $registration->shouldReceive('hasConfig','hasOpcao')->andReturn(false);
        $registration->shouldReceive('getCidade')->andReturn('Cascavel/PR - BRA');
        $registration->shouldReceive('getCreatedAt')->andReturn('14/11/2026 10:30');
        $player = \Mockery::mock(\App\Enxadrista::class)->makePartial();
        $player->forceFill(['id'=>2246,'name'=>'Nome do Enxadrista','cbx_id'=>42825,'fide_id'=>22792643]);
        $player->shouldReceive('getNomePrivado')->andReturn('Nome do Enxadrista');
        $player->shouldReceive('getNascimentoPrivado')->andReturn('05/11/1975');
        $player->shouldReceive('showRating')->andReturn(1798);
        $player->setRelation('sexo', (object)['id'=>1,'name'=>'Masculino']);
        $category = \Mockery::mock(\App\Categoria::class)->makePartial();
        $category->forceFill(['id'=>112,'name'=>'Aberto']);
        $category->shouldReceive('isPaid')->andReturn(true);
        $tournament = new Torneio;
        $tournament->forceFill(['id'=>2]);
        $tournament->setRelation('evento',$event)->setRelation('software',new class { public function isChessCom() { return false; } });
        $registration->setRelation('torneio',$tournament)->setRelation('enxadrista',$player)->setRelation('categoria',$category);
        $registration->setRelation('cidade',(object)['name'=>'Cascavel'])->setRelation('clube',null)->setRelation('from',null);
        $event->shouldReceive('getInscricoes')->andReturn(collect([$registration]));
        $event->shouldReceive('enxadristaInscrito')->andReturn($registration);
        return ['evento'=>$event,'torneio'=>$tournament,'inscricoes'=>collect([$registration])];
    }

    /** @dataProvider lists */
    public function test_every_column_is_preserved_and_actions_are_icon_groups($view)
    {
        $html = view($view,$this->fixture())->render();
        if (getenv('V2_CAPTURE')) {
            file_put_contents(storage_path(strpos($view, 'torneio') === false ? 'review-event.html' : 'review-tournament.html'), $html);
        }
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new \DOMXPath($dom);
        $headers=$xpath->query('//table/thead/tr/th');
        $cells=$xpath->query('//table/tbody/tr[1]/td');
        $this->assertSame($headers->length,$cells->length);
        $this->assertGreaterThan(10,$headers->length);
        $this->assertContains('ID FIDE',$html);
        $this->assertContains('ID CBX',$html);
        $this->assertContains('v2-registration-actions',$html);
        $this->assertContains('aria-label="Editar"',$html);
        $this->assertContains('aria-label="Excluir inscrição paga"',$html);
        $this->assertContains('data-placement="top"',$html);
        $this->assertContains('name="confirmation" value="delete_paid_registration"',$html);
        $this->assertSame(0,$xpath->query('//tbody/th')->length);
        foreach($xpath->query('//*[contains(@class,"v2-icon-action")]') as $action) {
            $this->assertNotEmpty($action->getAttribute('aria-label'));
            $this->assertSame('',trim($action->textContent));
        }
        if(strpos($view,'torneio')===false) {
            $this->assertContains('Campo adicional',$html);
            $this->assertNotContains('box-title evento',$html);
        }
    }

    /** @dataProvider lists */
    public function test_permissions_still_hide_paid_deletion_and_edit_actions($view)
    {
        $html=view($view,$this->fixture(false))->render();
        $this->assertNotContains('js-delete-paid-registration',$html);
        $this->assertNotContains('aria-label="Editar"',$html);
    }

    public function lists()
    {
        return [['evento.v2._content.inscricoes'],['evento.v2._content.torneio.inscricao.index']];
    }
}
