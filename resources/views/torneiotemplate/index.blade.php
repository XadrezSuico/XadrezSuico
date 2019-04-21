@extends('adminlte::page')

@section('title', 'Templates de Torneio')

@section('content_header')
    <h1>Templates de Torneio</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/torneiotemplate/new")}}">Novo Template de Torneio</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Nome do Torneio</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($torneios_template as $torneio_template)
                        <tr>
                            <td>{{$torneio_template->id}}</td>
                            <td>{{$torneio_template->name}}</td>
                            <td>{{$torneio_template->torneio_name}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/torneiotemplate/dashboard/".$torneio_template->id)}}" role="button">Dashboard</a>
                                @if($torneio_template->isDeletavel()) <a class="btn btn-danger" href="{{url("/torneiotemplate/delete/".$torneio_template->id)}}" role="button">Apagar</a> @endif
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
