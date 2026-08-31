@if(!empty($resultado) && !empty($resultado['parecer']))
    @php
        $classe = 'parecer-nao-confere';
        if ($resultado['parecer'] === 'Confere') {
            $classe = 'parecer-confere';
        } elseif ($resultado['parecer'] === 'Verificar') {
            $classe = 'parecer-verificar';
        } elseif ($resultado['parecer'] === 'Nome não integrado') {
            $classe = 'parecer-nao-integrado';
        }
    @endphp
    <span class="parecer-badge {{ $classe }}" title="{{ $resultado['detalhe'] ?? '' }}">
        {{ $resultado['parecer'] }}
    </span>
@else
    -
@endif
