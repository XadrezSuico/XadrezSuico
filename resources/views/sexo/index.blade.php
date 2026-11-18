@extends('layouts.v2.catalog')

@section('title', 'Sexos')

@section('page-header')
    Sexos
@endsection

@section('catalog-content')
    <ul class="v2-catalog-nav">
        <li role="presentation"><a href="{{url("/sexo/new")}}">Novo Sexo</a></li>
    </ul>

    <div class="v2-catalog-panel">
        <div class="v2-catalog-panel-body">
            <table id="tabela" data-dashboard-managed="true" class="v2-catalog-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Abreviação</th>
                        <th class="v2-reg-options">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sexos as $sexo)
                        <tr>
                            <td>{{$sexo->id}}</td>
                            <td>{{$sexo->name}}</td>
                            <td>{{$sexo->abbr}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/sexo/edit/".$sexo->id)}}" role="button">Editar</a>
                                @if($sexo->isDeletavel()) <a class="btn btn-danger" href="{{url("/sexo/delete/".$sexo->id)}}" role="button">Apagar</a> @endif
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
