@extends('adminlte::page')

@section('title', "Gestão de Vínculos Federativos >> Vínculo")

@section('content_header')
    <h1>Gestão de Vínculos Federativos >> Vínculo</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif


    <div class="alert alert-warning alert-dismissible">
        <h4><i class="icon fa fa-warning"></i> Aviso!</h4>
        Esta tela não serve de consulta ao vínculo para comprovação. Ela serve apenas para gerenciamento do vínculo.
    </div>

    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/fexpar/vinculos")}}"><< Voltar a Lista de Vínculos</a></li>
        @if($vinculo)
            <li role="presentation"><a href="{{url("/especiais/fexpar/vinculos/".$vinculo->uuid)}}" target="_blank">Link Público deste Vínculo (Para copiar com o botão direito do mouse)</a></a></li>
        @endif
    </ul>
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Gerenciamento de Vínculo</h3>
                </div>
                <div class="box-body">
                    <h4>Enxadrista:</h4>
                    <h5><strong>Nome:</strong> {{$enxadrista->name}}</h5>
                    <h5><strong>ID FEXPAR:</strong> {{$enxadrista->id}}</h5>
                    <h5><strong>ID CBX:</strong> {{$enxadrista->cbx_id}}</h5>
                    <h5><strong>ID FIDE:</strong> {{$enxadrista->fide_id}}</h5>
                    <hr/>
                    @if(!$vinculo)
                        <h4>Não possui vínculo.</h4>
                    @else
                        <h3>Vínculo <strong>#{{$vinculo->uuid}}</strong></h3>
                        <h4><strong>Ano:</strong> {{$vinculo->ano}}</h4>
                        <h5><strong>Tipo de Vínculo:</strong> {{$vinculo->getVinculoType()}}</h5>
                        <hr/>
                        <h4>Vínculo:</h4>
                        <h5><strong>Cidade:</strong> {{$vinculo->cidade->name}}</h5>
                        <h5><strong>Clube:</strong> {{$vinculo->clube->name}}</h5>
                        <hr/>
                        <h4>Dados:</h4>
                        @if($vinculo->is_confirmed_system)
                            <h5><strong>Quantos eventos que o enxadrista participou competindo por este clube:</strong> {{$vinculo->system_inscricoes_in_this_club_confirmed}} (Valor em <strong>{{$vinculo->getCreatedAt()}}</strong>)</h5>
                            <small>Observação: Esta informação compreende apenas os registros de eventos que constam no XadrezSuíço que atendam os seguintes requisitos:
                                <ul>
                                    <li>Esteja com a inscrição com a mesma cidade e clube do vínculo;</li>
                                    <li>O evento em questão esteja devidamente homologado.</li>
                                </ul>
                                Vale salientar também que, o que vale é o ID de Cadastro de Clube quando é efetuada a validação do clube.
                            </small>
                        @endif
                        @if($vinculo->is_confirmed_manually)
                            <h5><strong>Eventos Jogados:</strong></h5>
                            <p>{!!$vinculo->events_played!!}</p>
                            <h4>Dados Internos:</h4>
                            <h5><strong>Observações:</strong></h5>
                            <p>{!!$vinculo->obs!!}</p>
                        @endif
                        <hr/>
                        <h5><strong>Eventos via XadrezSuíço (Informação obtida durante a consulta):</strong></h5>
                        <table class="table" width="100%">
                            <thead>
                                <tr>
                                    <th>ID do Evento</th>
                                    <th>ID da Inscrição</th>
                                    <th>Grupo de Evento</th>
                                    <th>Evento</th>
                                    <th>Torneio</th>
                                </tr>
                            </thead>
                            @foreach($vinculo->enxadrista->getInscricoesByClube($vinculo->clube->id) as $inscricao)
                                <tr>
                                    <td>{{$inscricao->torneio->evento->id}}</td>
                                    <td>{{$inscricao->id}} (UUID: {{$inscricao->uuid}})</td>
                                    <td>{{$inscricao->torneio->evento->grupo_evento->name}}</td>
                                    <td>{{$inscricao->torneio->evento->name}}</td>
                                    <td>{{$inscricao->torneio->name}}</td>
                                </tr>
                            @endforeach
                        </table>
                        <hr/>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @if(!$vinculo)
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Realizar Vínculo</h3>
                    </div>
                    <form method="post">
                        @csrf
                        <div class="box-body">
                            <h4>Vínculo:</h3>
                            <div class="form-group">
                                <label for="cidade_id">Cidade *</label>
                                <select id="cidade_id" name="cidade_id" class="cidade_id form-control">
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="clube_id">Clube *</label>
                                <select id="clube_id" name="clube_id" class="clube_id form-control">
                                </select>
                            </div>
                            <small><strong>IMPORTANTE!</strong> Para o clube estar aqui ele precisa ter a opção "É clube válido para vinculo federativo?" marcada no cadastro de clube.</small>
                            <hr/>
                            <h4>Dados:</h4>
                            <label for="events_played">Eventos Jogados: *</label>
                            <textarea id="events_played" name="events_played" class="form-control" rows="3"></textarea>
                            <h4>Dados Internos:</h4>
                            <label for="obs">Observações: *</label>
                            <textarea id="obs" name="obs" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-success" type="submit">Salvar</button>
                        </div>
                    </form>
                </div>
            @else
                @if($vinculo->is_confirmed_system)
                    <div class="box" id="system_vinculo_alert">
                        <div class="box-header">
                            <h3 class="box-title">Gerenciar Vínculo Automático</h3>
                        </div>
                        <div class="box-body">
                            <p>Este enxadrista possui um vínculo automático gerenciado pelo Sistema XadrezSuíço.</p>
                            <p>Caso deseje, é possível transformar este vínculo em um vínculo manual, e assim permitir que seja alterado cidade e clube.</p>
                            <button type="button" class="btn btn-lg btn-block btn-danger" id="change_vinculo_to_manual">Desejo transformar em vínculo manual</button>
                        </div>
                    </div>
                @endif
                <div class="box box-default @if($vinculo->is_confirmed_system) collapsed-box @endif" id="edit_box">
                    <div class="box-header">
                        <h3 class="box-title">Editar Vínculo</h3>
                    </div>
                    <form method="post">
                        @csrf
                        <div class="box-body">
                            @if($vinculo->is_confirmed_system)
                                <button type="button" class="btn btn-lg btn-block btn-danger" id="change_vinculo_to_system">Cancelar - Voltar ao vínculo automático.</button>
                            @endif
                            <h4>Vínculo:</h3>
                            <div class="form-group">
                                <label for="cidade_id">Cidade *</label>
                                <select id="cidade_id" name="cidade_id" class="cidade_id form-control">
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="clube_id">Clube *</label>
                                <select id="clube_id" name="clube_id" class="clube_id form-control">
                                </select>
                            </div>
                            <small><strong>IMPORTANTE!</strong> Para o clube estar aqui ele precisa ter a opção "É clube válido para vinculo federativo?" marcada no cadastro de clube.</small>
                            <hr/>
                            <h4>Dados:</h4>
                            <label for="events_played">Eventos Jogados: *</label>
                            <textarea id="events_played" name="events_played" class="form-control" rows="3">{{$vinculo->events_played}}</textarea>
                            <h4>Dados Internos:</h4>
                            <label for="obs">Observações: *</label>
                            <textarea id="obs" name="obs" class="form-control" rows="3">{{$vinculo->obs}}</textarea>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-success" type="submit">Salvar</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#cidade_id").select2({
            ajax: {
                url: '{{url("/cidade/api/searchList/16")}}',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        $("#clube_id").select2({
            ajax: {
                url: '{{url("/clube/api/searchList?is_fexpar___clube_valido_vinculo_federativo=true")}}',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        @if($vinculo)
            var newOptionCidade = new Option("{{$vinculo->cidade->name}}", "{{$vinculo->cidade->id}}", false, false);
            $('#cidade_id').append(newOptionCidade).trigger('change');
            $("#cidade_id").val("{{$vinculo->cidade->id}}").change();

            var newOptionClube = new Option("{{$vinculo->clube->name}}", "{{$vinculo->clube->id}}", false, false);
            $('#clube_id').append(newOptionClube).trigger('change');
            $("#clube_id").val("{{$vinculo->clube->id}}").change();
        @endif

        $("#change_vinculo_to_manual").on("click",function(){
			$("#system_vinculo_alert").boxWidget('collapse');
			$("#edit_box").boxWidget('expand');
        });
        $("#change_vinculo_to_system").on("click",function(){
			$("#system_vinculo_alert").boxWidget('expand');
			$("#edit_box").boxWidget('collapse');
        });
    });
</script>
@endsection
@section("css")
    <style>
        .form-control, .select2{
            width: 100% !important;
        }
    </style>
@stop
