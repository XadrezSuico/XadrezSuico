<table>
    <tbody>
        <tr>
            <td colspan="12" style="border: 1px solid: #000 !important; text-align: center">
                <h1 style="margin:0;font-weight:bold">{{$evento->name}}</h1>
            </td>
        </tr>
        <tr>
            <td colspan="12" style="border: 1px solid: #000 !important; text-align: center">
                <h2 style="margin:0;font-weight:bold">FICHA DE CONFIRMAÇÃO</h2>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid: #000 !important">
                INSTITUIÇÃO:
            </td>
            <td colspan="8" style="border: 1px solid: #000 !important">
                {{$clube->getName()}}
            </td>
        </tr>
        @foreach(
            $evento->inscritosPorClube($clube->id) as $id_categoria => $inscricoes
        )
            <tr>
                <td colspan="12" style="background: #000; COLOR: #FFF; TEXT-ALIGN: CENTER">
                    CATEGORIA: {{$evento->categorias()->where([["categoria_id","=",$id_categoria]])->first()->categoria->name}}
                </td>
            </tr>

            <tr>
                <td colspan="1" style="background: #000; COLOR: #FFF; TEXT-ALIGN: CENTER">
                    Nº
                </td>
                <td colspan="7" style="background: #000; COLOR: #FFF; TEXT-ALIGN: CENTER">
                    ATLETA
                </td>
                <td colspan="2" style="background: #000; COLOR: #FFF; TEXT-ALIGN: CENTER">
                    RELÂMPAGO
                </td>
                <td colspan="2" style="background: #000; COLOR: #FFF; TEXT-ALIGN: CENTER">
                    RÁPIDO
                </td>
            </tr>
            @php($i = 1)
            @foreach($inscricoes as $inscricao)

                <tr>
                    <td colspan="1" style="TEXT-ALIGN: CENTER">
                        {{$i++}}
                    </td>
                    <td colspan="7" style="TEXT-ALIGN: CENTER">
                        {{$inscricao->enxadrista->name}}
                    </td>
                    <td colspan="2" style="TEXT-ALIGN: CENTER">
                        @if($evento->tipo_modalidade == 2 && $fill_blanks) @if($inscricao->confirmado) X @endif @endif
                    </td>
                    <td colspan="2" style="TEXT-ALIGN: CENTER">
                        @if($evento->tipo_modalidade == 1 && $fill_blanks) @if($inscricao->confirmado) X @endif @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="12" style="border: 1px solid: #000 !important; text-align: center">
                <p style="text-align: center">ASSINATURA DO PROFESSOR</p>
            </td>
        </tr>
    </tbody>
</table>
