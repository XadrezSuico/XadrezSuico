@extends('adminlte::page')

@php
        $permitido_edicao = false;
        if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventsByPerfil([4])
        ){
            $permitido_edicao = true;
        }
@endphp

@section('title', 'Enxadristas')

@section('content_header')
    <h1>Enxadristas</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if($permitido_edicao)
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/enxadrista/new")}}">Novo Enxadrista</a></li>
        </ul>
    @endif
    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>IDs</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enxadristas as $enxadrista)
                        <tr>
                            <td>{{$enxadrista->id}}</td>
                            <td>{{$enxadrista->name}}</td>
                            <td>{{$enxadrista->getBorn()}}</td>
                            <td>@if($enxadrista->sexos_id) {{$enxadrista->sexo->name}} @else - @endif</td>
                            <td>
                                @if($enxadrista->cbx_id) CBX: {{$enxadrista->cbx_id}} <br/>@endif
                                @if($enxadrista->fide_id) FIDE: {{$enxadrista->fide_id}} <br/>@endif
                                @if($enxadrista->lbx_id) LBX: {{$enxadrista->lbx_id}} <br/>@endif
                            </td>
                            <td>{{$enxadrista->cidade->name}}</td>
                            <td>@if($enxadrista->clube) {{$enxadrista->clube->name}} @else Não possui clube @endif</td>
                            <td>
                                @if($permitido_edicao)
                                    <a class="btn btn-default" href="{{url("/enxadrista/edit/".$enxadrista->id)}}" role="button">Editar</a>
                                @else
                                    <a class="btn btn-default" href="{{url("/enxadrista/edit/".$enxadrista->id)}}" role="button">Visualizar</a>
                                @endif
                                @if($enxadrista->isDeletavel() && $permitido_edicao) <a class="btn btn-danger" href="{{url("/enxadrista/delete/".$enxadrista->id)}}" role="button">Apagar</a> @endif
                            </td>
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
        });
    });
</script>
@endsection
