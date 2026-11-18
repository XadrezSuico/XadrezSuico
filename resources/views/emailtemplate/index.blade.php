@extends('layouts.v2.catalog')

@section('title', 'Template de E-mail')

@section('page-header')
    Templates de E-mail
@endsection

@section('catalog-content')

    <div class="v2-catalog-panel">
        <div class="v2-catalog-panel-body">
            <table id="tabela" data-dashboard-managed="true" class="v2-catalog-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Assunto do E-mail</th>
                        <th class="v2-reg-options">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td>{{$template->id}}</td>
                            <td>{{$template->name}}</td>
                            <td>{{$template->subject}}</td>
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
        window.initV2DashboardManagedTable('#tabela');
    });
</script>
@endsection
