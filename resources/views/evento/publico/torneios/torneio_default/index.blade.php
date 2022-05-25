<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="torneio_header_{{$torneio->id}}">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#torneio_content_{{$torneio->id}}" aria-expanded="true" aria-controls="torneio_content_{{$torneio->id}}">
                {{$torneio->name}}
            </a>
        </h4>
    </div>
    <div id="torneio_content_{{$torneio->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="torneio_header_{{$torneio->id}}">
        <div class="panel-body">
            @foreach($torneio->rodadas->all() as $rodada)
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center"><strong>Rodada {{$rodada->numero}}</strong></h4>
                    </div>
                    @php($i = 1)
                    <table class="table table-striped table-bordered table-sm table-responsive">
                        <thead>
                            <tr>
                                <th>Mesa</th>
                                <th>Nº In.</th>
                                <th>Enxadrista (Brancas)</th>
                                <th class="text-center">#</th>
                                @if(false)
                                    <th class="text-center">In.</th>
                                    <th class="text-center">IW</th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">IL</th>
                                    <th class="text-center">Movimentação</th>
                                    <th class="text-center">Res.</th>
                                @endif
                                <th class="text-center">Resultado A</th>
                                <th class="text-center">Resultado B</th>
                                <th class="text-center">#</th>
                                @if(false)
                                    <th class="text-center">In.</th>
                                    <th class="text-center">IW</th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">IL</th>
                                    <th class="text-center">Movimentação</th>
                                    <th class="text-center">Res.</th>
                                @endif
                                <th>Enxadrista (Negras)</th>
                                <th>Nº In.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rodada->emparceiramentos()->orderBy("numero","ASC")->get() as $emparceiramento)
                                <tr>
                                    <td>{{$emparceiramento->numero}}</td>
                                    <td>{{$emparceiramento->numero_a}}</td>
                                    <td>#{{$emparceiramento->inscricao_A->enxadrista->id}} - {{$emparceiramento->inscricao_A->enxadrista->name}}</td>
                                    <td>{{$emparceiramento->inscricao_a}}</td>
                                    @if(false)
                                        <td>{{$emparceiramento->rating_a}}</td>
                                        <td>
                                            {{$emparceiramento->rating_a_if_win}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_a_if_drw}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_a_if_los}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_a_mov}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_a_final}}
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        {{$emparceiramento->getResultadoA()}}
                                    </td>
                                    <td class="text-center">
                                        {{$emparceiramento->getResultadoB()}}
                                    </td>
                                    <td>{{$emparceiramento->inscricao_b}}</td>
                                    @if(false)
                                        <td>{{$emparceiramento->rating_b}}</td>
                                        <td>
                                            {{$emparceiramento->rating_b_if_win}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_b_if_drw}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_b_if_los}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_b_mov}}
                                        </td>
                                        <td>
                                            {{$emparceiramento->rating_b_final}}
                                        </td>
                                    @endif
                                    <td>@if($emparceiramento->inscricao_B)#{{$emparceiramento->inscricao_B->enxadrista->id}} -  {{$emparceiramento->inscricao_B->enxadrista->name}} @else - @endif</td>
                                    <td>{{$emparceiramento->numero_b}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>
