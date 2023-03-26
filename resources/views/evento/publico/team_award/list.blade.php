@extends('adminlte::page')

@section('title', 'Evento #'.$event->id.' ('.$event->name.') - Resultados da Premiação de Times "'.$team_award->name.'"')

@section('content_header')
    <h1>Evento #{{$event->id}} ({{$event->name}}) - Resultados da Premiação de Times #{{$team_award->id}} ({{$team_award->name}})</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if(\Illuminate\Support\Facades\Auth::check())
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/".$event->id."/team_awards/standings")}}">Voltar à Seleção da Premiação de Time</a></li>
        </ul>
    @else
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/".$event->id."/team_awards/standings")}}">Voltar à Seleção da Premiação de Time</a></li>
        </ul>
	@endif

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cód</th>
                        <th>Clube</th>
                        <th>Cidade</th>
                        <th>Pontuação</th>
                        @php($i=1)
                        @foreach($team_award->tiebreaks()->orderBy("priority","ASC")->get() as $tiebreak)
                            <th>D{{$i++}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($team_award->team_scores()->orderBy("place","ASC")->get() as $score)
                        <tr>
                            <td>{{$score->place}}</td>
                            <td><a href="{{url("/evento/".$event->id."/team_awards/1/results/team/".$score->club->id)}}">#{{$score->club->id}}</a></td>
                            <td><a href="{{url("/evento/".$event->id."/team_awards/1/results/team/".$score->club->id)}}">{{$score->club->name}}</a></td>
                            <td>{{$score->club->cidade->name}}</td>
                            <td>{{$score->score}}</td>
                            @foreach($team_award->tiebreaks()->orderBy("priority","ASC")->get() as $tiebreak)
                                <td>
                                    {{$score->getTiebreak($tiebreak->tiebreak->id,$tiebreak->priority)}}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr/>
            <div class="row">
                <div class="col-md-6">
                    <h4>Legenda dos Critérios de Desempate:</h4><br/>
                    @php($j=1)
                    @foreach($team_award->tiebreaks()->orderBy("priority","ASC")->get() as $tiebreak)
                        <strong>D{{$j++}} - {{$tiebreak->tiebreak->code}}:</strong> {{$tiebreak->tiebreak->name}}<br/>
                    @endforeach
                </div>
                <div class="col-md-6">
                </div>
            </div>
        </div>
    </div>
@endsection

@section("js")
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: false
        });
        // $("#tabela").DataTable({
        //     responsive: true,
        // });
    });
</script>
@endsection
