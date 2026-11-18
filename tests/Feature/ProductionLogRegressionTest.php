<?php
namespace Tests\Feature;

use App\Http\Controllers\RatingController;
use App\Http\Controllers\EventoController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Tests\TestCase;

class ProductionLogRegressionTest extends TestCase
{
    private $dispatcher;
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:' . base64_encode(str_repeat('t', 32))]);
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        DB::purge('sqlite');
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();
        Schema::create('tipo_ratings', function ($t) { $t->increments('id'); });
        Schema::create('ratings', function ($t) { $t->increments('id');$t->integer('tipo_ratings_id');$t->integer('enxadrista_id');$t->integer('valor'); });
        Schema::create('enxadrista', function ($t) { $t->increments('id');$t->string('name'); });
        Schema::create('evento', function ($t) { $t->increments('id');$t->boolean('mostrar_resultados')->default(true); });
        Schema::create('categoria', function ($t) { $t->increments('id'); });
        Schema::create('torneio', function ($t) { $t->increments('id');$t->integer('evento_id'); });
        Schema::create('categoria_torneio', function ($t) { $t->increments('id');$t->integer('categoria_id');$t->integer('torneio_id'); });
        DB::table('tipo_ratings')->insert(['id'=>1]);
        DB::table('ratings')->insert(['id'=>1,'tipo_ratings_id'=>1,'enxadrista_id'=>999,'valor'=>1800]);
        DB::table('evento')->insert(['id'=>1]);DB::table('categoria')->insert(['id'=>1]);
    }
    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }
    public function test_rating_search_accepts_missing_datatables_parameters_and_ignores_orphan_players()
    {
        $result = (new RatingController)->searchRatingList(1, Request::create('/'))->getData(true);
        $this->assertSame(0, $result['draw']);
        $this->assertSame(0, $result['recordsTotal']);
        $this->assertSame([], $result['data']);
    }
    public function test_import_form_requires_login()
    {
        $this->get('/evento/1/torneios/1/resultados/file')->assertRedirect('/login');
    }
    public function test_results_without_linked_tournament_return_404()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        try { (new EventoController)->resultados_v2(1,1); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(404,$e->getStatusCode()); throw $e; }
    }
}
