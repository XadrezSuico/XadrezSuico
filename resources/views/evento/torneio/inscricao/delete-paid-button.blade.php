@if($inscricao->paid || $inscricao->getPaymentInfo('uuid'))
    @if(Auth::user()->hasPermissionGlobal() || Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) || Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7]))
        <form method="POST" action="{{ route('evento.torneios.inscricao.delete_paid', [$evento->id, $inscricao->torneio_id, $inscricao->id]) }}" class="js-delete-paid-registration" style="display:inline-block" data-player="{{ $inscricao->enxadrista->name }}">
            @csrf
            <input type="hidden" name="confirmation" value="delete_paid_registration">
            @if(isset($registrationList) && $registrationList)
                <input type="hidden" name="route" value="event_registration_list">
            @endif
            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Excluir inscrição paga</button>
        </form>
    @endif
@endif
