<?php

namespace Tests\Feature;

use App\Emparceiramento;
use App\Enum\RegistrationStatus;
use App\Http\Controllers\External\XadrezSuicoPagController;
use App\Http\Controllers\External\XadrezSuicoPagNotificationController;
use App\Http\Controllers\InscricaoGerenciarController;
use App\Inscricao;
use App\InscricaoCriterioDesempate;
use App\MovimentacaoRating;
use App\Torneio;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegistrationStatusTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'activitylog.enabled' => false, 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::purge('sqlite');
        Schema::create('inscricao', function (Blueprint $t) {
            $t->increments('id');
            $t->string('uuid')->nullable();
            $t->unsignedInteger('torneio_id');
            $t->unsignedInteger('categoria_id')->default(1);
            $t->unsignedInteger('enxadrista_id')->default(1);
            $t->boolean('paid')->default(false);
            $t->boolean('confirmado')->default(false);
            $t->text('payment_info')->nullable();
            $t->timestamps();
        });
        require_once base_path('database/migrations/2026_11_12_000000_add_status_to_inscricao_table.php');
        (new \AddStatusToInscricaoTable)->up();
        Schema::create('torneio', function (Blueprint $t) {
            $t->increments('id'); $t->integer('evento_id');
        });
        Schema::create('evento', function (Blueprint $t) {
            $t->increments('id'); $t->string('uuid')->nullable(); $t->integer('grupo_evento_id')->nullable();
        });
        Schema::create('event_team_awards', function (Blueprint $t) {
            $t->increments('id'); $t->integer('events_id');
        });
        Schema::create('event_team_scores', function (Blueprint $t) {
            $t->increments('id'); $t->integer('event_team_awards_id');
        });
        Schema::create('movimentacao_ratings', function (Blueprint $t) {
            $t->increments('id'); $t->integer('inscricao_id')->nullable(); $t->integer('ratings_id')->nullable(); $t->decimal('valor')->default(0);
        });
        Schema::create('ratings', function (Blueprint $t) { $t->increments('id'); $t->decimal('valor')->default(0); $t->timestamps(); });
        Schema::create('rating_dias', function (Blueprint $t) { $t->increments('id'); $t->integer('ratings_id'); });
        Schema::create('emparceiramentos', function (Blueprint $t) {
            $t->increments('id'); $t->integer('inscricao_a')->nullable(); $t->integer('inscricao_b')->nullable();
        });
        Schema::create('inscricao_criterio_desempate', function (Blueprint $t) {
            $t->increments('id'); $t->integer('inscricao_id');
        });
        DB::table('evento')->insert(['id' => 1, 'uuid' => 'event-1']);
        DB::table('torneio')->insert(['id' => 1, 'evento_id' => 1]);
        DB::table('inscricao')->insert([
            ['id' => 1, 'uuid' => 'active', 'torneio_id' => 1, 'status' => 'enabled'],
            ['id' => 2, 'uuid' => 'disabled', 'torneio_id' => 1, 'status' => 'disabled'],
            ['id' => 3, 'uuid' => 'deleted', 'torneio_id' => 1, 'status' => 'deleted'],
        ]);
    }

    public function test_defaults_and_all_normal_queries_exclude_only_deleted()
    {
        $this->assertSame('enabled', (new Inscricao)->status);
        DB::table('inscricao')->insert(['id' => 4, 'torneio_id' => 1]);
        $this->assertSame('enabled', DB::table('inscricao')->where('id', 4)->value('status'));
        $this->assertEquals([1, 2, 4], Inscricao::orderBy('id')->pluck('id')->all());
        $this->assertNull(Inscricao::find(3));
        $this->assertSame(3, Torneio::find(1)->inscricoes()->count());
        $this->assertSame(0, Torneio::whereHas('inscricoes', function ($q) { $q->where('uuid', 'deleted'); })->count());
        $this->assertSame(0, Inscricao::where('id', 3)->orWhere('uuid', 'deleted')->count());
        $this->assertSame(3, Inscricao::join('torneio', 'torneio.id', '=', 'inscricao.torneio_id')->count());
        $this->assertFalse(Inscricao::withoutGlobalScope('not_deleted')->find(3)->isPresent());
    }

    public function test_historical_pairs_movements_and_tiebreaks_are_excluded()
    {
        DB::table('emparceiramentos')->insert([
            ['id' => 1, 'inscricao_a' => 1, 'inscricao_b' => 2],
            ['id' => 2, 'inscricao_a' => 1, 'inscricao_b' => 3],
            ['id' => 3, 'inscricao_a' => 3, 'inscricao_b' => 1],
            ['id' => 4, 'inscricao_a' => 1, 'inscricao_b' => null],
        ]);
        DB::table('movimentacao_ratings')->insert([
            ['id' => 1, 'inscricao_id' => 1], ['id' => 2, 'inscricao_id' => 3], ['id' => 3, 'inscricao_id' => null],
        ]);
        DB::table('inscricao_criterio_desempate')->insert([
            ['id' => 1, 'inscricao_id' => 1], ['id' => 2, 'inscricao_id' => 3],
        ]);
        $this->assertEquals([1, 4], Emparceiramento::orderBy('id')->pluck('id')->all());
        $this->assertEquals([1, 3], MovimentacaoRating::orderBy('id')->pluck('id')->all());
        $this->assertEquals([1], InscricaoCriterioDesempate::pluck('id')->all());
        $this->assertSame(4, DB::table('emparceiramentos')->count());
    }

    public function test_paid_deletion_preserves_payment_and_invalidates_cached_lists()
    {
        Auth::shouldReceive('user')->andReturn(new class {
            public function hasPermissionGlobal() { return true; }
        });
        DB::table('inscricao')->where('id', 1)->update(['paid' => true, 'confirmado' => true,
            'payment_info' => json_encode(['uuid' => 'payment-1'])]);
        Cache::put('event_event-1_api_registration_public_list', 'old', 60);
        Cache::put('event_event-1_registration_public_list', 'old', 60);
        Cache::put('registration_active_v1_public_list', 'old', 60);
        $response = (new InscricaoGerenciarController)->delete_paid(1, 1, 1,
            Request::create('/', 'POST', ['confirmation' => 'delete_paid_registration']));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertNull(Inscricao::find(1));
        $registration = Inscricao::withoutGlobalScope('not_deleted')->find(1);
        $this->assertSame(RegistrationStatus::Deleted, $registration->status);
        $this->assertSame('payment-1', $registration->getPaymentInfo('uuid'));
        $this->assertFalse((bool) $registration->confirmado);
        $this->assertSame(3, DB::table('inscricao')->count());
        $this->assertFalse(Cache::has($registration->torneio->evento->getCacheKey('api_registration_public_list')));
        $this->assertFalse(Cache::has($registration->torneio->evento->getCacheKey('registration_public_list')));
        $this->assertFalse(Cache::has('registration_active_v1_public_list'));
    }

    public function test_existing_rating_totals_stop_counting_deleted_movements()
    {
        Auth::shouldReceive('user')->andReturn(new class {
            public function hasPermissionGlobal() { return true; }
        });
        DB::table('inscricao')->where('id', 1)->update(['paid' => true]);
        Auth::shouldReceive('getDefaultDriver')->andReturn('web');
        $guard = \Mockery::mock();
        $guard->shouldReceive('user')->andReturn(null);
        Auth::shouldReceive('guard')->andReturn($guard);
        DB::table('ratings')->insert(['id' => 1, 'valor' => 1025]);
        DB::table('movimentacao_ratings')->insert([
            ['inscricao_id' => null, 'ratings_id' => 1, 'valor' => 1000],
            ['inscricao_id' => 1, 'ratings_id' => 1, 'valor' => 25],
        ]);
        DB::table('rating_dias')->insert(['ratings_id' => 1]);
        (new InscricaoGerenciarController)->delete_paid(1, 1, 1,
            Request::create('/', 'POST', ['confirmation' => 'delete_paid_registration']));
        $this->assertEquals(1000, DB::table('ratings')->where('id', 1)->value('valor'));
        $this->assertSame(2, DB::table('movimentacao_ratings')->count());
        $this->assertSame(0, DB::table('rating_dias')->count());
    }

    public function test_paid_button_renders_with_escaped_player_name_and_post_route()
    {
        Auth::shouldReceive('user')->andReturn(new class {
            public function hasPermissionGlobal() { return true; }
        });
        $registration = Inscricao::find(1);
        $registration->paid = true;
        $player = new \App\Enxadrista;
        $player->name = '<Player>';
        $registration->setRelation('enxadrista', $player);
        $html = view('evento.torneio.inscricao.delete-paid-button', [
            'evento' => $registration->torneio->evento, 'inscricao' => $registration, 'registrationList' => true,
        ])->render();
        $this->assertContains('method="POST"', $html);
        $this->assertContains('/inscricoes/delete-paid/1', $html);
        $this->assertContains('data-player="&lt;Player&gt;"', $html);
        $this->assertContains('name="_token"', $html);
    }

    public function test_deletion_requires_manager_permission()
    {
        Auth::shouldReceive('user')->andReturn(new class {
            public function hasPermissionGlobal() { return false; }
            public function hasPermissionEventByPerfil($id, $profiles) { return false; }
            public function hasPermissionGroupEventByPerfil($id, $profiles) { return false; }
        });
        Schema::create('grupo_evento', function (Blueprint $t) { $t->increments('id'); });
        DB::table('grupo_evento')->insert(['id' => 1]);
        DB::table('evento')->where('id', 1)->update(['grupo_evento_id' => 1]);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new InscricaoGerenciarController)->delete_paid(1, 1, 1,
            Request::create('/', 'POST', ['confirmation' => 'delete_paid_registration']));
    }

    public function test_deletion_cannot_target_another_tournament()
    {
        Auth::shouldReceive('user')->andReturn(new class {
            public function hasPermissionGlobal() { return true; }
        });
        DB::table('torneio')->insert(['id' => 2, 'evento_id' => 1]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new InscricaoGerenciarController)->delete_paid(1, 2, 1,
            Request::create('/', 'POST', ['confirmation' => 'delete_paid_registration']));
    }

    public function test_deletion_requires_confirmation()
    {
        Auth::shouldReceive('user')->andReturn(new class {
            public function hasPermissionGlobal() { return true; }
        });
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new InscricaoGerenciarController)->delete_paid(1, 1, 1, Request::create('/', 'POST'));
    }

    public function test_deletion_cannot_archive_a_registration_without_payment()
    {
        Auth::shouldReceive('user')->andReturn(new class {
            public function hasPermissionGlobal() { return true; }
        });
        try {
            (new InscricaoGerenciarController)->delete_paid(1, 1, 1,
                Request::create('/', 'POST', ['confirmation' => 'delete_paid_registration']));
            $this->fail('Unpaid and unlinked registration must be rejected.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
            $this->assertSame('enabled', Inscricao::find(1)->status);
        }
    }

    /** @dataProvider paymentStatuses */
    public function test_payment_callback_updates_deleted_registration_without_confirming_or_emailing($paymentStatus, $expectedPaid)
    {
        putenv('XADREZSUICOPAG_SYSTEM_ID=status-test');
        DB::table('inscricao')->where('id', 3)->update(['payment_info' => json_encode(['uuid' => 'payment-3'])]);
        $controllers = new \ReflectionProperty(XadrezSuicoPagController::class, 'controllers');
        $controllers->setAccessible(true);
        $old = $controllers->getValue();
        $controllers->setValue(null, ['registration' => new class($paymentStatus) {
            private $status;
            public function __construct($status) { $this->status = $status; }
            public function get($uuid) { return (object) ['ok' => 1, 'registration' => (object) ['status' => $this->status]]; }
        }]);
        try {
            $request = Request::create('/', 'POST', ['registration_uuid' => 'payment-3']);
            $request->headers->set('System-Id', 'status-test');
            $response = (new XadrezSuicoPagNotificationController)->notification('deleted', $request);
            $this->assertSame(200, $response->getStatusCode());
            $registration = Inscricao::withoutGlobalScope('not_deleted')->find(3);
            $this->assertSame('deleted', $registration->status);
            $this->assertSame($expectedPaid, (bool) $registration->paid);
            $this->assertFalse((bool) $registration->confirmado);
            $this->assertNull(Inscricao::find(3));
        } finally {
            $controllers->setValue(null, $old);
            putenv('XADREZSUICOPAG_SYSTEM_ID');
        }
    }

    public function paymentStatuses()
    {
        return [['paid', true], ['refunded', false]];
    }
}
