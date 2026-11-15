<?php
namespace Tests\Feature;

use App\Services\TournamentCategoryBatch;
use App\Torneio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TournamentCategoryBatchTest extends TestCase
{
    private $dispatcher;
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite', 'database.connections.sqlite.database'=>':memory:']);
        DB::purge('sqlite');
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();
        foreach (['evento','torneio','categoria_torneio','inscricao'] as $name) {
            Schema::create($name, function ($t) use ($name) {
                $t->increments('id');
                if ($name === 'evento') $t->integer('grupo_evento_id')->default(1);
                elseif ($name === 'torneio') { $t->integer('evento_id'); $t->integer('tipo_torneio_id')->default(1); $t->integer('torneio_template_id')->nullable(); }
                else { $t->integer('torneio_id'); $t->integer('categoria_id'); if ($name === 'inscricao') $t->string('status')->default('enabled'); }
                $t->timestamps();
            });
        }
        DB::table('evento')->insert([['id'=>1],['id'=>2]]);
        DB::table('torneio')->insert([
            ['id'=>1,'evento_id'=>1,'tipo_torneio_id'=>1,'torneio_template_id'=>10],
            ['id'=>2,'evento_id'=>1,'tipo_torneio_id'=>1,'torneio_template_id'=>20],
            ['id'=>3,'evento_id'=>2,'tipo_torneio_id'=>1,'torneio_template_id'=>30],
            ['id'=>4,'evento_id'=>1,'tipo_torneio_id'=>3,'torneio_template_id'=>40],
        ]);
        DB::table('categoria_torneio')->insert([
            ['id'=>11,'torneio_id'=>1,'categoria_id'=>101],['id'=>12,'torneio_id'=>1,'categoria_id'=>102],
            ['id'=>13,'torneio_id'=>1,'categoria_id'=>103],['id'=>21,'torneio_id'=>2,'categoria_id'=>101],
        ]);
        DB::table('inscricao')->insert([
            ['id'=>1,'torneio_id'=>1,'categoria_id'=>101,'status'=>'enabled'],
            ['id'=>2,'torneio_id'=>1,'categoria_id'=>102,'status'=>'disabled'],
            ['id'=>3,'torneio_id'=>1,'categoria_id'=>103,'status'=>'enabled'],
            ['id'=>4,'torneio_id'=>1,'categoria_id'=>101,'status'=>'deleted'],
        ]);
    }
    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }
    public function test_transfer_moves_selected_categories_and_registrations_and_merges_existing_link()
    {
        $count=(new TournamentCategoryBatch)->execute(Torneio::find(1),[11,12],'transfer',2);
        $this->assertSame(2,$count);
        $this->assertEquals([2,2,1,1],DB::table('inscricao')->orderBy('id')->pluck('torneio_id')->all());
        $this->assertEquals([101,102],DB::table('categoria_torneio')->where('torneio_id',2)->orderBy('categoria_id')->pluck('categoria_id')->all());
        $this->assertSame(1,DB::table('categoria_torneio')->where('torneio_id',1)->count());
        $this->assertNull(Torneio::find(1)->torneio_template_id);
        $this->assertNull(Torneio::find(2)->torneio_template_id);
    }
    public function test_delete_removes_only_selected_links_and_preserves_registrations()
    {
        $before=DB::table('inscricao')->orderBy('id')->get()->toJson();
        (new TournamentCategoryBatch)->execute(Torneio::find(1),[11,12],'delete');
        $this->assertSame($before,DB::table('inscricao')->orderBy('id')->get()->toJson());
        $this->assertEquals([13,21],DB::table('categoria_torneio')->orderBy('id')->pluck('id')->all());
    }
    /** @dataProvider invalidBatches */
    public function test_invalid_batch_changes_nothing($ids,$action,$target)
    {
        try { (new TournamentCategoryBatch)->execute(Torneio::find(1),$ids,$action,$target); $this->fail('Expected validation failure'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); }
        $this->assertSame(4,DB::table('categoria_torneio')->count());
        $this->assertSame(4,DB::table('inscricao')->where('torneio_id',1)->count());
        $this->assertEquals(10,Torneio::find(1)->torneio_template_id);
    }
    public function invalidBatches()
    {
        return [[[11,21],'delete',null],[[11,999],'transfer',2],[[11],'transfer',3],[[11],'transfer',1],[[11,12],'transfer',4],[[],'delete',null]];
    }
    private function authorize($allowed)
    {
        $this->withoutMiddleware();
        $user=\Mockery::mock();
        $user->shouldReceive('hasPermissionGlobal')->andReturn($allowed);
        $user->shouldReceive('hasPermissionEventByPerfil')->andReturn(false);
        $user->shouldReceive('hasPermissionGroupEventByPerfil')->andReturn(false);
        Auth::shouldReceive('user')->andReturn($user);
    }
    public function test_endpoint_requires_permission_and_confirmation()
    {
        $this->authorize(false);
        $this->postJson('/evento/1/torneios/1/categoria/batch',['action'=>'delete','category_ids'=>[11],'confirmation'=>'delete_categories'])->assertStatus(403);
        $this->assertSame(4,DB::table('categoria_torneio')->count());
    }
    public function test_endpoint_rejects_unconfirmed_delete_and_out_of_event_tournament()
    {
        $this->authorize(true);
        $this->postJson('/evento/1/torneios/1/categoria/batch',['action'=>'delete','category_ids'=>[11]])->assertStatus(422);
        $this->postJson('/evento/1/torneios/3/categoria/batch',['action'=>'delete','category_ids'=>[11],'confirmation'=>'delete_categories'])->assertStatus(404);
        $this->assertSame(4,DB::table('categoria_torneio')->count());
    }
    public function test_confirmed_endpoint_deletes_selected_link()
    {
        $this->authorize(true);
        $this->post('/evento/1/torneios/1/categoria/batch',['action'=>'delete','category_ids'=>[11],'confirmation'=>'delete_categories'])
            ->assertRedirect('/evento/1/torneios/edit/1');
        $this->assertSame(3,DB::table('categoria_torneio')->count());
    }
}
