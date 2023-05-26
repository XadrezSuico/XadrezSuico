<table>
    <thead>
    <tr>
        <th>ID_No</th>
        @if($evento->usa_lbx && !$evento->tipo_rating) <th>Fide_No</th> @endif
        <th>Name</th>
        <th>Fed</th>
        <th>Clubnumber</th>
        <th>ClubName</th>
        <th>Birthday</th>
        @if($evento->usa_cbx && !$evento->tipo_rating)<th>Rtg_Nat</th>@endif
        @if($evento->usa_lbx || $evento->usa_fide || $evento->tipo_rating)<th>Rtg_Int</th>@endif
    </tr>
    </thead>
    <tbody>
    @foreach($enxadristas as $enxadrista)
        <tr>
            <td>{{ $enxadrista->id }}</td>
            @if($evento->usa_lbx && !$evento->tipo_rating) <td>{{ $enxadrista->lbx_id }}</td> @endif
            <td>{{ $enxadrista->getNameToSM() }}</td>
            <td>BRA</td>
            <td>{{ $enxadrista->cidade->id }}</td>
            <td>{{ $enxadrista->cidade->name }} @if($enxadrista->clube) - {{$enxadrista->clube->getName()}} @endif</td>
            <td>{{ $enxadrista->getBornToSM() }}</td>

            @if($evento->usa_cbx && !$evento->tipo_rating)<td>{{ $enxadrista->showRating(1,$evento->tipo_modalidade) }}</td>@endif
            @if($evento->tipo_rating)
                <td>
                    @if($enxadrista->showRatingInterno($evento->tipo_rating->id))
                        {{ $enxadrista->showRatingInterno($evento->tipo_rating->id) }}
                    @else
                        {{ $evento->tipo_rating->tipo_rating->showRatingRegraIdade($enxadrista->howOld(),$evento) }}
                    @endif
                </td>
            @endif

            @if($evento->usa_fide)<td>{{ $enxadrista->showRating(0,$evento->tipo_modalidade, $evento->getConfig("fide_sequence")) }}</td>@endif
            @if($evento->usa_lbx)<td>{{ $enxadrista->showRating(2,$evento->tipo_modalidade) }}</td>@endif



        </tr>
    @endforeach
    </tbody>
</table>
