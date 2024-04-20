<table>
    <thead>
    <tr>
        <th>ID_No</th>
        <th>Fide_No</th>
        <th>Name</th>
        <th>Fed</th>
        <th>Clubnumber</th>
        <th>ClubName</th>
        <th>Birthday</th>
        <th>Rtg_Nat</th>
        <th>Rtg_Int</th>
    </tr>
    </thead>
    <tbody>
        @foreach($evento->torneios->all() as $torneio)
            @foreach($torneio->inscricoes->all() as $inscricao)
                <tr>
                    <td>@if($evento->usa_cbx && !$evento->tipo_rating)
                            @if($inscricao->enxadrista->cbx_id)
                                {{ $inscricao->enxadrista->cbx_id }}
                            @else
                                XZ{{ $inscricao->enxadrista->id }}
                            @endif
                        @else
                            XZ{{ $inscricao->enxadrista->id }}
                        @endif
                    </td>
                    <td>
                        @if($evento->usa_lbx)
                            {{ $inscricao->enxadrista->lbx_id }}
                        @endif
                        @if($evento->usa_fide)
                            {{ $inscricao->enxadrista->fide_id }}
                        @endif
                    </td>
                    <td>{{ $inscricao->enxadrista->getNameToSM() }}</td>
                    <td>@if($inscricao->clube) {{ mb_strtoupper($inscricao->clube->abbr) }} @endif</td>
                    <td>{{ $inscricao->cidade->id }}</td>
                    <td>{{ $inscricao->cidade->getName() }} @if($inscricao->clube) - {{$inscricao->clube->getName()}} @endif</td>
                    <td>{{ $inscricao->enxadrista->getBornToSM() }}</td>
                    <td>
                        @if($evento->usa_cbx && !$evento->tipo_rating)
                            {{ $inscricao->enxadrista->showRating(1,$evento->tipo_modalidade) }}
                        @elseif($evento->tipo_rating)
                            @if($inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id))
                                {{ $inscricao->enxadrista->showRatingInterno($evento->tipo_rating->id) }}
                            @else
                                {{ $evento->tipo_rating->tipo_rating->showRatingRegraIdade($inscricao->enxadrista->howOld(),$evento) }}
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($evento->usa_fide)
                            {{ $inscricao->enxadrista->showRating(0,$evento->tipo_modalidade, $evento->getConfig("fide_sequence")) }}
                        @elseif($evento->usa_lbx)
                            {{ $inscricao->enxadrista->showRating(2,$evento->tipo_modalidade) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
