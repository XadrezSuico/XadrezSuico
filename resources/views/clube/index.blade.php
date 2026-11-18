@extends('layouts.v2.catalog')

@section('title', 'Clubes')

@section('page-header')
    Clubes
@endsection

@section('catalog-content')
    <ul class="v2-catalog-nav">
        <li role="presentation"><a href="{{url("/clube/new")}}">Novo Clube</a></li>
    </ul>

    <div class="v2-catalog-panel">
        <div class="v2-catalog-panel-body">
            <table id="tabela" data-dashboard-managed="true" class="v2-catalog-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Cidade</th>
                        <th class="v2-reg-options">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clubes as $clube)
                        <tr>
                            <td>{{$clube->id}}</td>
                            <td>{{$clube->getName()}}</td>
                            <td>@if($clube->cidade) {{$clube->cidade->name}} @else - @endif</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/clube/edit/".$clube->id)}}" role="button">Editar</a>
                                @if($clube->isDeletavel()) <a class="btn btn-danger" href="{{url("/clube/delete/".$clube->id)}}" role="button">Apagar</a> @endif
                                <a class="btn btn-warning" href="{{url("/clube/union/".$clube->id)}}" role="button">Unir para Este Clube</a>
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
        window.initV2DashboardManagedTable('#tabela');
    });
</script>
@endsection
