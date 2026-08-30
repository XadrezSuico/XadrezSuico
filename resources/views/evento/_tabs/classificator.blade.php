                <br/>
                <section class="col-lg-12 connectedSortable">
                    @foreach($evento->event_classificates->all() as $event_classificates)

                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">{{config("xadrezsuico.name","XadrezSuíço")}} Classificador - Regras e Processos de Classificação para o Evento #{{$event_classificates->event->id}} - {{$event_classificates->event->name}}</h3>
                            </div>
                            <!-- form start -->
                                <div class="box-body">
                                    @php($total_classified = $event_classificates->howMuchClassificated())
                                    @if($total_classified > 0)
                                        <div class="alert alert-success" role="alert">
                                            <strong>Classificado!</strong><br/>
                                            O presente classificador foi classificado.<br/>
                                            Total de Classificados: {{$total_classified}}
                                        </div>
                                    @endif
                                    <ul class="nav nav-pills">
                                        <li role="presentation"><a href="{{url("/classificator/event/".$evento->id."/".$event_classificates->id."/process")}}">!!!! Processar Classificações (Use com cuidado)</a></li>
                                        <li role="presentation"><a href="{{url("/classificator/event/".$evento->id."/".$event_classificates->id."/classificated/delete")}}">!!!! Remover classificados</a></li>
                                        @if($total_classified > 0)
                                            <li role="presentation"><a href="{{url("/inscricao/classificados/".$event_classificates->id)}}">[PÚBLICO] Lista de Classificados</a></li>
                                        @endif
                                    </ul>

                                    <ul class="nav nav-pills">
                                        <li role="presentation"><a href="{{url("/classificator/event/".$evento->id."/".$event_classificates->id."/rule/new")}}">Nova Regra</a></li>
                                    </ul>
                                    <table id="tabela_classificators" class="table-responsive table-condensed table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Regra</th>
                                                <th>Configurações</th>
                                                @if($total_classified > 0)
                                                    <th>Total de Classificados</th>
                                                @endif
                                                <th width="20%">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($event_classificates->rules->all() as $rule)
                                                <tr>
                                                    <td>{{$rule->id}}</td>
                                                    <td>{{$rule->getRuleName()}}</td>
                                                    <td>
                                                        @switch($rule->type)
                                                            @case(\App\Enum\ClassificationTypeRule::POSITION)
                                                                Posição Relativa: {{$rule->value}}
                                                            @break
                                                            @case(\App\Enum\ClassificationTypeRule::POSITION_ABSOLUTE)
                                                                Posição Absoluta: {{$rule->value}}
                                                            @break
                                                            @case(\App\Enum\ClassificationTypeRule::PRE_CLASSIFICATE)
                                                                Pré-classificação pelo Evento: #{{$rule->event->id}} - {{$rule->event->name}}
                                                            @break
                                                            @case(\App\Enum\ClassificationTypeRule::PLACE_BY_QUANTITY)
                                                                @if($rule->is_absolute)
                                                                    Vagas a Cada: {{$rule->value}} (Completos).
                                                                @else
                                                                    Vagas a Cada: {{$rule->value}} (ou fração).
                                                                @endif
                                                            @break
                                                            @case(\App\Enum\ClassificationTypeRule::CLASSIFICATE_BY_START_POSITION)
                                                                Posição na Classificação Inicial: {{$rule->value}}
                                                            @break
                                                        @endswitch
                                                        @if($rule->configs()->count() > 0)
                                                            <hr/>
                                                            <label>Regras adicionais:</label>
                                                            @foreach(ClassificationTypeRuleConfig::list() as $key => $type_config)
                                                                @if($rule->hasConfig($key))
                                                                    <br/> {{$type_config["name"]}}: {{$type_config["type"] == "boolean" ? "Sim" : $rule->getConfig($key,true)}}
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    @if($total_classified > 0)
                                                        <td>{{$rule->howMuchClassificated()}}</td>
                                                    @endif
                                                    <td>
                                                        <a class="btn btn-default" href="{{url("/classificator/event/".$evento->id."/".$event_classificates->id."/rule/edit/".$rule->id)}}" role="button">Editar</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                        </div>
                    @endforeach
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">{{config("xadrezsuico.name","XadrezSuíço")}} Classificador</h3>
                        </div>
                        <!-- form start -->
                            <div class="box-body">

                                <ul class="nav nav-pills">
                                    <li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/new")}}">Novo Classificador</a></li>
                                </ul>
                                <table id="tabela_classificators" class="table-responsive table-condensed table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Classificador à Este</th>
                                            <th width="20%">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->event_classificators->all() as $event_classificate)
                                            <tr>
                                                <td>{{$event_classificate->id}}</td>
                                                <td>
                                                    @if($event_classificate->event_classificator)
                                                        <strong>Evento:</strong>
                                                        {{$event_classificate->event_classificator->name}}<br/>
                                                        <small>Grupo de Evento: {{$event_classificate->event_classificator->grupo_evento->name}}</small>
                                                    @else
                                                        <strong>Grupo de Evento:</strong>
                                                        {{$event_classificate->event_group_classificator->name}}
                                                    @endif
                                                </td>
                                                <td>

                                                    @if($event_classificate->event_classificator)
                                                        @if(
                                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($event_classificate->event_classificator->id,[3,4,5]) ||
                                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($event_classificate->event_classificator->grupo_evento->id,[6,7])
                                                        )
                                                            <a class="btn btn-warning mr-1" href="{{url("/evento/dashboard/".$event_classificate->event_classificator->id)}}" role="button">Acessar Dashboard do Evento</a>
                                                        @endif
                                                    @endif
                                                    @if($event_classificate->event_group_classificator)
                                                        @if(
                                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfilByGroupEvent($event_classificate->event_group_classificator->id,[3,4,5]) ||
                                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($event_classificate->event_group_classificator->id,[6,7])
                                                        )
                                                            <a class="btn btn-warning mr-1" href="{{url("/grupoevento/dashboard/".$event_classificate->event_group_classificator->id)}}" role="button">Acessar Dashboard do Grupo de Evento</a>
                                                        @endif
                                                    @endif
                                                    <a class="btn btn-default" href="{{url("/evento/".$evento->id."/classificator/edit/".$event_classificate->id)}}" role="button">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                    </div>
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Categorias Vinculadas</h3>
                        </div>
                        <!-- form start -->
                            <div class="box-body">

                                <ul class="nav nav-pills">
                                    <li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/category/new")}}">Novo Vínculo de Categoria</a></li>
                                </ul>
                                <table id="tabela_classificators" class="table-responsive table-condensed table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Categoria Base</th>
                                            <th>Categoria deste Evento</th>
                                            <th width="20%">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->categorias_cadastradas->all() as $categoria)
                                            @foreach($categoria->event_classificates->all() as $event_class_category)
                                                <tr>
                                                    <td>{{$event_class_category->id}}</td>
                                                    <td>
                                                        {{$event_class_category->category->id}} - {{$event_class_category->category->name}}<br/>
                                                        @if($event_class_category->category->evento)
                                                            Evento: {{$event_class_category->category->evento->name}}
                                                        @else
                                                            Grupo de Evento: {{$event_class_category->category->grupo_evento->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$event_class_category->category_classificator->id}} - {{$event_class_category->category_classificator->name}}<br/>
                                                        @if($event_class_category->category_classificator->evento)
                                                            Evento: {{$event_class_category->category_classificator->evento->name}}
                                                        @else
                                                            Grupo de Evento: {{$event_class_category->category_classificator->grupo_evento->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-default" href="{{url("/evento/".$evento->id."/classificator/category/edit/".$event_class_category->id)}}" role="button">Editar</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                    </div>
                </section>
            
