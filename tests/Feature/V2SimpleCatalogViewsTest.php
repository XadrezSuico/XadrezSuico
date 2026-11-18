<?php
namespace Tests\Feature;

use Tests\TestCase;

class V2SimpleCatalogViewsTest extends TestCase
{
    /** @dataProvider pages */
    public function test_catalog_pages_render_with_fields_actions_and_plugins_preserved($page)
    {
        $directory = storage_path('framework/testing/catalog-layout');
        @mkdir($directory . '/layouts/v2', 0777, true);
        file_put_contents($directory . '/layouts/v2/app.blade.php', '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="{{ asset("css/app-v2.css") }}">@stack("styles")</head><body><main style="padding:16px"><h1>@yield("page-header")</h1>@yield("content")</main><script>window.uiErrors=[];window.addEventListener("error",function(e){uiErrors.push(e.message)});</script><script src="{{ asset("vendor/adminlte/vendor/jquery/dist/jquery.min.js") }}"></script><script src="{{ asset("vendor/datatables/datatables.min.js") }}"></script><script src="{{ asset("vendor/datatables/DataTables-1.10.18/js/dataTables.bootstrap.min.js") }}"></script>@stack("scripts")@yield("js")<script>jQuery(function(){window.fixtureReady=true;});</script></body></html>');
        app('view')->getFinder()->prependLocation($directory);
        app('view')->getFinder()->flush();
        $city = new \App\Cidade; $city->forceFill(['id'=>1,'name'=>'Cidade de teste com nome extenso']);
        $sex = \Mockery::mock(\App\Sexo::class)->makePartial();$sex->forceFill(['id'=>1,'name'=>'Feminino','abbr'=>'F']);$sex->shouldReceive('isDeletavel')->andReturn(true);
        $club = \Mockery::mock(\App\Clube::class)->makePartial();$club->forceFill(['id'=>1,'name'=>'Clube de teste','abbr'=>'ABC','cidade_id'=>1]);$club->setRelation('cidade',$city);
        $club->shouldReceive('getName')->andReturn('Clube de teste');$club->shouldReceive('getPlace')->andReturn('Cidade de teste');$club->shouldReceive('isDeletavel')->andReturn(true);
        $template = \Mockery::mock(\App\EmailTemplate::class)->makePartial();$template->forceFill(['id'=>1,'name'=>'Template de teste','subject'=>'Assunto de teste','message'=>'<p>Mensagem {enxadrista.name}</p>','email_type'=>1]);
        $template->setRelation('evento',null)->setRelation('grupo_evento',null);$template->shouldReceive('getEmailType')->andReturn('Inscrição');
        $html = view($page, ['cidade'=>$city,'cidades'=>collect([$city]),'sexo'=>$sex,'sexos'=>collect([$sex]),'clube'=>$club,'clube_base'=>$club,'clubes'=>collect([$club]),'email_template'=>$template,'templates'=>collect([$template])])->render();
        $this->assertStringContainsString('v2-catalog', $html);
        $this->assertStringNotContainsString('@endsection', $html);
        $this->assertStringNotContainsString('connectedSortable', $html);
        if (strpos($page,'.index') === false) {
            $this->assertStringContainsString('name="_token"', $html);
            $this->assertStringContainsString('method="post"', $html);
        } else {
            $this->assertStringContainsString('initV2DashboardManagedTable', $html);
            $this->assertStringContainsString('v2-reg-options', $html);
        }
        if ($page === 'emailtemplate.edit') {
            $this->assertStringContainsString('CKEDITOR.replace', $html);
            $this->assertStringContainsString('&lt;p&gt;Mensagem {enxadrista.name}&lt;/p&gt;', $html);
        }
        if ($output = getenv('V2_CATALOG_FIXTURES')) {
            file_put_contents($output . '/' . $page . '.html', str_replace('http://localhost/', 'file:///tmp/xadrez-fexpar-log-fixes/public/', $html));
        }
    }
    public function pages()
    {
        return array_map(function($page){return [$page];}, ['sexo.index','sexo.new','sexo.edit','cidade.index','cidade.new','cidade.edit','clube.index','clube.new','clube.edit','clube.union','emailtemplate.index','emailtemplate.edit']);
    }
}
