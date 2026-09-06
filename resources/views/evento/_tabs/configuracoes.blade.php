@php
    $can_edit = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );
    $peso_atual = $evento->getClassificacaoGeralPeso();
    $peso_configurado = $evento->hasConfig('classificacao_geral_peso');
@endphp

<div class="v2-configuracoes-tab">
    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            @if(!$evento->classificavel)
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <strong>Atenção!</strong> Este evento não está marcado como <em>classificável</em> na classificação geral do grupo.
                    O peso configurado abaixo só terá efeito após ativar essa opção e reclassificar o evento e o grupo.
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <section class="col-lg-8 connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Classificação do Grupo de Evento</h3>
                </div>
                @if($can_edit)
                    <form method="post" action="{{ url('/evento/' . $evento->id . '/configuracoes') }}">
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <label for="classificacao_geral_peso">Peso na classificação geral</label>
                                <input
                                    type="number"
                                    name="classificacao_geral_peso"
                                    id="classificacao_geral_peso"
                                    class="form-control"
                                    step="0.01"
                                    min="0.01"
                                    value="{{ $peso_configurado ? $peso_atual : '' }}"
                                    placeholder="1"
                                />
                                <p class="help-block">
                                    Multiplicador aplicado aos pontos deste evento na classificação geral do grupo.
                                    Valores menores que 1 reduzem a pontuação, igual a 1 mantém o valor original e maiores que 1 aumentam.
                                    Deixe em branco ou informe 1 para usar o peso padrão.
                                </p>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Salvar configurações</button>
                        </div>
                    </form>
                @else
                    <div class="box-body">
                        <p><strong>Peso na classificação geral:</strong> {{ $peso_atual }}</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="col-lg-4 connectedSortable">
            <div class="box box-default">
                <div class="box-header">
                    <h3 class="box-title">Informações</h3>
                </div>
                <div class="box-body">
                    <p>
                        Após alterar o peso, é necessário <strong>reclassificar este evento</strong> e, em seguida,
                        <strong>reclassificar o grupo de evento</strong> para que a pontuação geral seja recalculada.
                    </p>
                    <p class="text-muted mb-0">
                        Peso efetivo atual: <strong>{{ $peso_atual }}</strong>
                        @if(!$peso_configurado)
                            (padrão)
                        @endif
                    </p>
                </div>
            </div>
        </section>
    </div>
</div>
