<?php
namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Tests\TestCase;

class V2CityListTest extends TestCase
{
    public function test_city_created_without_state_can_be_displayed_in_the_list()
    {
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        DB::purge('sqlite');
        $dispatcher = Model::getEventDispatcher();Model::unsetEventDispatcher();
        try {
            Schema::create('cidade',function($t){$t->increments('id');$t->string('name');$t->integer('estados_id')->nullable();$t->integer('ibge_id')->nullable();});
            Schema::create('estados',function($t){$t->increments('id');$t->string('nome');$t->string('abbr');$t->integer('pais_id');});
            Schema::create('pais',function($t){$t->increments('id');$t->string('name');$t->string('codigo_iso');});
            foreach(['clube','enxadrista','inscricao','evento'] as $table) {
                Schema::create($table,function($t){$t->increments('id');$t->integer('cidade_id');$t->string('status')->default('enabled');});
            }
            DB::table('cidade')->insert(['id'=>1,'name'=>'Cidade sem estado']);
            $user = \Mockery::mock(\App\User::class)->makePartial();$user->shouldReceive('hasPermissionGlobalbyPerfil')->andReturn(true);Auth::setUser($user);
            $request = Request::create('/', 'GET', ['search'=>['value'=>''],'order'=>[['column'=>0,'dir'=>'asc']],'length'=>10,'start'=>0,'draw'=>1]);
            $result = (new \App\Http\Controllers\CidadeController)->searchList(0,$request)->getData(true);
            $this->assertSame(1, $result['recordsTotal']);
            $this->assertSame('Cidade sem estado', $result['data'][0][1]);
            $this->assertSame('-', $result['data'][0][2]);
            $this->assertSame('-', $result['data'][0][3]);
            $this->assertStringContainsString('/cidade/edit/1', $result['data'][0][4]);
        } finally { Model::setEventDispatcher($dispatcher); }
    }
}
