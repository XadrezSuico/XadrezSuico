@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
    <h1>Usuários</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/usuario/new")}}">Novo Usuário</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th width="25%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/usuario/edit/".$user->id)}}" role="button">Editar</a>
                                @if($user->isDeletavel()) <a class="btn btn-danger" href="{{url("/usuario/delete/".$user->id)}}" role="button">Apagar</a> @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
