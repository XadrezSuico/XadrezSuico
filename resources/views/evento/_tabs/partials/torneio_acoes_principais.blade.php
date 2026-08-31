@if(
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
)
    <div class="v2-torneio-actions__group">
        <span class="v2-torneio-actions__label">Gerenciar</span>
        <div class="v2-torneio-actions__buttons">
            <a class="btn btn-default btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/edit/' . $torneio->id) }}">Editar</a>
            @if($torneio->tipo_torneio->id != 3)
                <a class="btn btn-warning btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/union/' . $torneio->id) }}">Unir Torneios</a>
            @endif
            @if(!$evento->e_resultados_manuais && !$torneio->evento->is_lichess_integration && !$torneio->software->isChessCom())
                <a class="btn btn-default btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/resultados/file') }}">Resultados</a>
                <a class="btn btn-default btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/emparceiramentos') }}">Emparceiramentos</a>
            @endif
            @if($torneio->tipo_torneio->id == 3)
                <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/gerenciamento/torneio_3') }}">Gerenciamento do Torneio</a>
            @endif
        </div>
    </div>

    <div class="v2-torneio-actions__group">
        <span class="v2-torneio-actions__label">Exportar inscrições</span>
        <div class="v2-torneio-actions__buttons">
            <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/sm') }}" target="_blank">Confirmadas</a>
            @if(
                env('XADREZSUICOPAG_URI', null) &&
                env('XADREZSUICOPAG_SYSTEM_ID', null) &&
                env('XADREZSUICOPAG_SYSTEM_TOKEN', null) &&
                $evento->isPaid()
            )
                <a class="btn btn-warning btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/sm/paid') }}" target="_blank">Pagas</a>
            @endif
            <a class="btn btn-warning btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/sm/all') }}" target="_blank">Todas</a>
            @if($evento->exportacao_sm_modelo == 6)
                <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/sm/teams/confirmed') }}" target="_blank">Times confirmados</a>
                <a class="btn btn-warning btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/sm/teams') }}" target="_blank">Todos os times</a>
            @endif
        </div>
    </div>
@endif

<div class="v2-torneio-actions__group">
    <span class="v2-torneio-actions__label">Consultar</span>
    <div class="v2-torneio-actions__buttons">
        <a class="btn btn-default btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes') }}">Inscrições</a>
        <a class="btn btn-info btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/relatorio/inscricoes') }}" target="_blank">Imprimir</a>
        <a class="btn btn-info btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/relatorio/inscricoes/alfabetico') }}" target="_blank">Imprimir (A–Z)</a>
        <a class="btn btn-info btn-sm" href="{{ url('/evento/' . $evento->id . '/torneios/' . $torneio->id . '/inscricoes/relatorio/inscricoes/alfabetico/cidade') }}" target="_blank">Imprimir (Cidade/Clube)</a>
    </div>
</div>
