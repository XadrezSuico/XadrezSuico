@php
    $can_edit = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($grupo_evento->id, [7])
    );
    $por_categoria = $grupo_evento->classificaIndividualPorCategoria();
    $geral = $grupo_evento->classificaIndividualGeral();
@endphp

<div class="grupoevento-configuracoes-tab">
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

    <section class="col-lg-8 connectedSortable">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Classificação Individual</h3>
            </div>
            @if($can_edit)
                <form method="post" action="{{ url('/grupoevento/' . $grupo_evento->id . '/configuracoes') }}">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="classificacao_individual_por_categoria" id="classificacao_individual_por_categoria"
                                    @if($por_categoria) checked @endif />
                                Classifica por categoria
                            </label>
                            <p class="help-block">
                                Mantém o comportamento atual: ranking separado para cada categoria do grupo.
                            </p>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="classificacao_individual_geral" id="classificacao_individual_geral"
                                    @if($geral) checked @endif />
                                Classifica geral (cross-categoria)
                            </label>
                            <p class="help-block">
                                Gera um ranking único recalculando cada etapa sem considerar categorias,
                                agregando os resultados em uma classificação geral do grupo.
                            </p>
                        </div>
                        @if(!$por_categoria && !$geral)
                            <div class="alert alert-warning">
                                Nenhum modo de classificação está ativo. Marque ao menos uma opção para permitir a classificação do grupo.
                            </div>
                        @endif
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Salvar configurações</button>
                    </div>
                </form>
            @else
                <div class="box-body">
                    <p><strong>Classifica por categoria:</strong> {{ $por_categoria ? 'Sim' : 'Não' }}</p>
                    <p><strong>Classifica geral:</strong> {{ $geral ? 'Sim' : 'Não' }}</p>
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
                    Após alterar os modos de classificação, é necessário
                    <strong>reclassificar o grupo de evento</strong> para recalcular os rankings.
                </p>
                <p class="text-muted mb-0">
                    Por padrão, todos os grupos classificam apenas por categoria.
                </p>
            </div>
        </div>
    </section>
</div>
