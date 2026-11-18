<?php
namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\MessageBag;
use Tests\TestCase;

class V2AccountPagesTest extends TestCase
{
    private $dispatcher;
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);DB::purge('sqlite');
        $this->dispatcher=Model::getEventDispatcher();Model::unsetEventDispatcher();
        Schema::create('grupo_evento',function($t){$t->increments('id');$t->string('name');});
        Schema::create('evento',function($t){$t->increments('id');$t->string('name');$t->integer('grupo_evento_id');});
        DB::table('grupo_evento')->insert(['id'=>1,'name'=>'Grupo de teste']);DB::table('evento')->insert(['id'=>1,'name'=>'Evento de teste','grupo_evento_id'=>1]);
        $directory=storage_path('framework/testing/account-layout');@mkdir($directory.'/layouts/v2',0777,true);
        file_put_contents($directory.'/layouts/v2/app.blade.php','<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="{{ asset("css/app-v2.css") }}">@stack("styles")</head><body><main style="padding:16px"><h1>@yield("page-header")</h1>@yield("content")</main><script>window.uiErrors=[];window.addEventListener("error",function(e){uiErrors.push(e.message)});</script><script src="{{ asset("vendor/adminlte/vendor/jquery/dist/jquery.min.js") }}"></script><script src="{{ asset("vendor/datatables/datatables.min.js") }}"></script><script src="{{ asset("vendor/datatables/DataTables-1.10.18/js/dataTables.bootstrap.min.js") }}"></script>@stack("scripts")@yield("js")<script>jQuery(function(){window.fixtureReady=true;});</script></body></html>');
        app('view')->getFinder()->prependLocation($directory);app('view')->getFinder()->flush();
    }
    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);parent::tearDown();
    }
    /** @dataProvider pages */
    public function test_pages_preserve_permissions_and_authentication_forms($page,$admin)
    {
        $actor=\Mockery::mock(\App\User::class)->makePartial();$actor->forceFill(['id'=>1,'name'=>'Administrador','email'=>'admin@example.test']);
        $actor->shouldReceive('hasPermissionGlobal','hasPermissionGlobalbyPerfil')->andReturn($admin);
        $actor->shouldReceive('hasPermissionGroupEventsByPerfil','hasPermissionGroupEventByPerfil','hasPermissionEventByPerfilByGroupEvent','hasPermissionEventByPerfil')->andReturn(true);
        if(strpos($page,'usuario.')===0)Auth::setUser($actor);
        $user=\Mockery::mock(\App\User::class)->makePartial();$user->forceFill(['id'=>2,'name'=>'Usuário de teste','email'=>'test@example.test']);$user->shouldReceive('isDeletavel')->andReturn(true);
        $profile=new \App\PerfilUser;$profile->id=9;$profile->setRelation('perfil',(object)['id'=>3,'name'=>'Diretor de Torneio']);$profile->setRelation('evento',(object)['id'=>1,'name'=>'Evento de teste','grupo_evento'=>(object)['id'=>1]]);$user->setRelation('perfis',collect([$profile]));
        $data=['user'=>$user,'users'=>collect([$user]),'USER'=>$actor,'ok'=>false,'news'=>[['name'=>'1.0','news'=>['Melhoria de teste']]],'token'=>'synthetic-reset-token','email'=>'test@example.test','errors'=>new ViewErrorBag];
        $html=view($page,$data)->render();
        $this->assertStringNotContainsString('@endsection',$html);
        if(strpos($page,'usuario.')===0){
            $this->assertStringContainsString('v2-catalog',$html);
            if($page==='usuario.edit'){
                $this->assertSame($admin,strpos($html,'1 - Super-Administrador')!==false);
                $this->assertStringContainsString('/usuario/2/perfis/add',$html);
                $this->assertStringContainsString('/usuario/2/perfis/remove/9',$html);
                if(!$admin)$this->assertStringContainsString('disabled="disabled"',$html);
            }
        }elseif(strpos($page,'auth.passwords.')===0){
            $this->assertStringContainsString('name="_token"',$html);
            $this->assertStringContainsString('name="email"',$html);
            $this->assertStringContainsString('v2-account',$html);
            if($page==='auth.passwords.reset'){
                $this->assertStringContainsString('synthetic-reset-token',$html);
                $this->assertStringContainsString('name="password_confirmation"',$html);
                $this->assertStringContainsString('action="'.route('password.update').'"',$html);
            }else $this->assertStringContainsString('action="'.route('password.email').'"',$html);
        }else $this->assertStringContainsString('v2-document',$html);
        if($output=getenv('V2_ACCOUNT_FIXTURES')){
            file_put_contents($output.'/'.$page.($admin?'':'.limited').'.html',str_replace('http://localhost/','file:///tmp/xadrez-fexpar-log-fixes/public/',$html));
        }
    }
    public function pages()
    {
        return [['usuario.index',true],['usuario.new',true],['usuario.edit',true],['usuario.edit',false],['usuario.password',true],['whatsnew',true],['termosdeuso',true],['politicadeprivacidade',true],['auth.passwords.email',true],['auth.passwords.reset',true]];
    }
    public function test_reset_validation_errors_and_email_are_displayed_without_filling_passwords()
    {
        $errors=(new ViewErrorBag)->put('default',new MessageBag(['email'=>'E-mail inválido','password'=>'Senha inválida']));
        $html=view('auth.passwords.reset',['token'=>'test-token','email'=>'saved@example.test','errors'=>$errors])->render();
        $this->assertStringContainsString('Senha inválida',$html);
        $this->assertStringContainsString('saved@example.test',$html);
        $this->assertStringContainsString('aria-invalid="true"',$html);
        $this->assertStringNotContainsString('name="password" value=',$html);
    }
}
