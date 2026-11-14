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
        <th>Title</th>
    </tr>
    </thead>
    <tbody>
        @foreach($evento->torneios->all() as $torneio)
            @foreach($torneio->inscricoes->all() as $inscricao)
                @php($identity = \App\Support\SwissManagerExportIdentity::forRegistration($inscricao, $evento->exportacao_sm_modelo))
                <tr>
                    <td>@if($evento->usa_cbx && !$evento->tipo_rating)
                            @if($inscricao->enxadrista->cbx_id)
                                @if(intval($inscricao->enxadrista->cbx_id) > 0)
                                    {{ $inscricao->enxadrista->cbx_id }}
                                @else
                                    XZ{{ $inscricao->enxadrista->id }}
                                @endif
                            @else
                                XZ{{ $inscricao->enxadrista->id }}
                            @endif
                        @else
                            {{ $inscricao->enxadrista->id }}
                        @endif
                    </td>
                    <td>
                        @if($evento->usa_lbx)
                            {{ intval($inscricao->enxadrista->lbx_id) }}
                        @endif
                        @if($evento->usa_fide)
                            {{ intval($inscricao->enxadrista->fide_id) }}
                        @endif
                    </td>
                    <td>{{ $identity['name'] }}</td>
                    <td>@if($inscricao->clube) {{ mb_strtoupper($inscricao->clube->abbr) }} @endif</td>
                    <td>{{ $identity['club_number'] }}</td>
                    <td>{{ $identity['club_name'] }}</td>
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
                    <td>
                        @if ($inscricao->enxadrista->titles()->count() > 0)
                            {{$inscricao->enxadrista->getTitle()->title->abbr}}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
