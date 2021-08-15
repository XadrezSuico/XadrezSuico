@extends('adminlte::page')

@section('title', 'Template de E-mail')

@section('content_header')
    <h1>Templates de E-mail</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Assunto do E-mail</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td>{{$template->id}}</td>
                            <td>{{$template->name}}</td>
                            <td>{{$template->abbr}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/emailtemplate/edit/".$template->id)}}" role="button">Editar</a>
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
