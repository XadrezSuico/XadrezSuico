@extends("relatorio.default")

@section('title', 'Relatórios > Inscrições')
@section('title_page_1', $evento->name)
@section('title_page_2', "Inscritos no Torneio '".$torneio->name."'")
@section('content')
<table border="1">
    <thead>
        <th>#</th>
        <th>Nome Completo</th>
        @if($evento->tipo_rating) <th>Rating</th> @endif
        @if($evento->usa_fide) <th>FIDE</th> @endif
        @if($evento->usa_cbx) <th>CBX</th> @endif
        <th>Categoria</th>
        <th>Cidade</th>
        <th>Clube</th>
    </thead>
    <tbody>
        @foreach($inscritos as $inscricao)
            <tr>
                <td>{{$inscricao->enxadrista_id}}</td>
                <td>{{$inscricao->enxadrista->getName()}}</td>
                @if($evento->tipo_rating) <td>{{$inscricao->enxadrista->ratingParaEvento($evento->id)}}</td> @endif
                @if($evento->usa_fide) <td>{{$inscricao->enxadrista->fide_rating}}</td> @endif
                @if($evento->usa_cbx) <td>{{$inscricao->enxadrista->cbx_rating}}</td> @endif
                <td>{{$inscricao->categoria->name}}</td>
                <td>{{$inscricao->cidade->name}}</td>
                <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else Sem Clube @endif</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection