@if(
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
)
    @if($torneio->isDeletavel())
        <div class="v2-torneio-actions__group">
            <span class="v2-torneio-actions__label">Avançado</span>
            <div class="v2-torneio-actions__buttons">
                <a class="btn btn-danger btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/delete/' . $torneio->id) }}">Apagar</a>
            </div>
        </div>
    @endif
@endif

@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && $evento->torneios()->count() > 1)
    <div class="v2-torneio-actions__group">
        <span class="v2-torneio-actions__label">Admin</span>
        <div class="v2-torneio-actions__buttons">
            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" title="Separar em um novo evento: {{ $torneio->id }} {{ $torneio->name }}" data-target="#modalSeparate_{{ $torneio->id }}">Separar em novo evento</button>
        </div>
    </div>
    <div class="modal fade modal-danger" id="modalSeparate_{{ $torneio->id }}" tabindex="-1" role="dialog" aria-labelledby="modalSeparateLabel_{{ $torneio->id }}">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalSeparateLabel_{{ $torneio->id }}">Separar em um novo evento #{{ $torneio->id }}: {{ $torneio->name }}</h4>
                </div>
                <div class="modal-body">
                    <h2>Você tem certeza que pretende fazer isso?</h2><br>
                    <h4>Assim que efetuar <strong>NÃO SERÁ POSSÍVEL</strong> retornar a configuração anterior.</h4>
                    <h4>Você deseja efetuar a separação?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Não quero mais</button>
                    <a class="btn btn-danger" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/migrate_to_new_event') }}">Sim, quero separar em um novo evento (Admin)</a>
                </div>
            </div>
        </div>
    </div>
@endif

@if($torneio->evento->is_lichess_integration)
    <div class="v2-torneio-actions__group">
        <span class="v2-torneio-actions__label">Lichess.org</span>
        <div class="v2-torneio-actions__buttons">
            <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/lichess/check_players_in') }}">Conferir inscrições</a>
            @if($torneio->evento->data_inicio <= date('Y-m-d'))
                <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/lichess/get_results') }}">Inserir resultados</a>
            @endif
            <a class="btn btn-danger btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/lichess/remove_lichess_players_not_found') }}">Remover players não encontrados</a>
        </div>
        <small class="text-muted">Última atualização: {{ $torneio->getLastLichessPlayersUpdate() }}</small>
    </div>
@endif

@if($torneio->software->isChessCom())
    <div class="v2-torneio-actions__group">
        <span class="v2-torneio-actions__label">Chess.com</span>
        @if($torneio->hasConfig('chesscom_tournament_slug'))
            <div class="v2-torneio-actions__buttons">
                <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/chesscom/check_players_in') }}">Conferir inscrições</a>
                @if($torneio->evento->data_inicio <= date('Y-m-d'))
                    <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/chesscom/get_results') }}">Importar resultados</a>
                @endif
            </div>
            <small class="text-muted">Última atualização: {{ $torneio->getLastChessComPlayersUpdate() }}</small>
        @else
            <p class="text-danger v2-torneio-actions__error">Configure o slug do torneio no Chess.com em Editar torneio.</p>
        @endif
    </div>
@endif

@if($evento->grupo_evento->hasConfig('is_pr_esporte', true))
    <div class="v2-torneio-actions__group">
        <span class="v2-torneio-actions__label">Paraná Esporte</span>
    </div>
@endif
