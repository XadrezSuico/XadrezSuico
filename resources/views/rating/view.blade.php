@extends('adminlte::page')

@section('title', 'Tipo de Rating #'.$tipo_rating->id." - Rating de ".$enxadrista->name)

@section("css")
<link rel="stylesheet" href="{{asset("/js/morris.js/morris.css")}}">
@endsection

@section('content_header')
    <h1>Tipo de Rating #{{$tipo_rating->id}} ({{$tipo_rating->name}}) - Rating de {{$enxadrista->name}}</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/rating/list/".$tipo_rating->id)}}">Voltar à Lista de Ratings</a></li>
    </ul>

    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Cadastro</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
        <div class="box-body">
                <h3 style="margin: 0; padding: 0;"><strong>Nome:</strong> {{$enxadrista->name}}<br/></h3>
                <strong>Data de Nascimento:</strong> {{$enxadrista->getBorn()}}<br/>
                <strong>Cidade:</strong> {{$enxadrista->cidade->name}}<br/>
                @if($enxadrista->clube) <strong>Clube:</strong> {{$enxadrista->clube->name}}<br/> @endif

                <h4><strong>Rating Atual:</strong> {{$rating->valor}}</h4>
        </div>
    </div>
    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Gráfico de Movimentação de Rating</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
        <div class="box-body">
              <div class="chart" id="rating-chart" style="position: relative; height: 300px;"></div>
        </div>
    </div>
    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Movimentações de Rating</h3>
			<div class="pull-right box-tools">
              <div class="chart" id="sales-chart" style="position: relative; height: 300px;"></div>
			</div>
		</div>
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grupo de Evento</th>
                        <th>Evento</th>
                        <th>Torneio</th>
                        <th>Inicial?</th>
                        <th>Movimentação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rating->movimentacoes()->orderBy("torneio_id","ASC")->get() as $movimentacao)
                        <tr>
                            <td>{{$movimentacao->id}}</td>
                            <td>@if(!$movimentacao->is_inicial) {{$movimentacao->torneio->evento->grupo_evento->name}} @else - @endif</td>
                            <td>@if(!$movimentacao->is_inicial) {{$movimentacao->torneio->evento->name}} @else - @endif</td>
                            <td>@if(!$movimentacao->is_inicial) {{$movimentacao->torneio->name}} @else - @endif</td>
                            <td>@if($movimentacao->is_inicial) Sim @else Não @endif</td>
                            <td>{{$movimentacao->valor}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section("js")
<script src="{{asset("/js/raphael/raphael.min.js")}}"></script>
<script src="{{asset("/js/morris.js/morris.min.js")}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
        });


        /* Morris.js Charts */
        // Sales chart
        var area = new Morris.Area({
            element   : 'rating-chart',
            resize    : true,
            data      : [
                @php($ratingGrafico = 0)
                @php($primeiraMovimentacaoRating = $rating->movimentacoes()->where([["is_inicial","=",0]])->orderBy("torneio_id","ASC")->first())
                @foreach($rating->movimentacoes()->orderBy("torneio_id","ASC")->get() as $movimentacao)
                    @if($movimentacao->is_inicial)
                        @php($ratingGrafico = $movimentacao->valor)
                        @if($primeiraMovimentacaoRating)
                            { y: '{{(date("Y-m-d",strtotime($primeiraMovimentacaoRating->torneio->evento->data_fim)-(60*60*24)))}}', rating: {{$ratingGrafico}} },
                        @else
                            { y: '{{$movimentacao->created_at}}', rating: {{$ratingGrafico}} },
                        @endif
                    @else
                        @php($ratingGrafico += $movimentacao->valor)
                        { y: '{{$movimentacao->torneio->evento->data_fim}}', rating: {{$ratingGrafico}} },
                    @endif
                    
                @endforeach
            ],
            xkey      : 'y',
            ykeys     : ['rating'],
            labels    : ['Rating'],
            lineColors: ['#a0d0e0'],
            hideHover : 'auto'
        });
        area.redraw();
    });
</script>
@endsection
