@extends('layouts.v2.catalog')

@section('title', 'Usuários')

@section('page-header')
    Usuários
@endsection

@section('catalog-content')
    <ul class="v2-catalog-nav">
        <li role="presentation"><a href="{{url("/usuario/new")}}">Novo Usuário</a></li>
    </ul>

    <div class="v2-catalog-panel">
        <div class="v2-catalog-panel-body">
            <table class="v2-catalog-table" style="width: 100%">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th class="v2-reg-options">Opções</th>
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
