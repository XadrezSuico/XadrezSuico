@extends('adminlte::page')

@section('title', "Enxadristas com Cadastro - FEXPAR")

@section('content_header')
    <h1>Enxadristas com Cadastro - FEXPAR</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <p>Esta lista compreende todos os cadastros existentes no sistema XadrezSuíço, preservando algumas informações considerando a LGPD.</p>
            <p>Vale constar que esta lista pode possuir registros duplicados, visto que a obrigação do preenchimento correto dos dados do enxadrista é do responsável pela inscrição do mesmo.</p>
            <hr/>
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th># - ID FEXPAR</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>ID CBX</th>
                        <th>ID FIDE</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enxadristas as $enxadrista)
                        <tr>
                            <td>{{$enxadrista->id}}</td>
                            <td>{{$enxadrista->name}}</td>
                            <td>{{$enxadrista->getNascimentoPublico()}}</td>
                            <td>{{$enxadrista->cbx_id}}</td>
                            <td>{{$enxadrista->fide_id}}</td>
                            <td>{{$enxadrista->cidade->name}}</td>
                            <td>@if($enxadrista->clube) {{$enxadrista->clube->name}} @else - @endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
            pageLength: 50
        });
    });
</script>
@endsection
