<table>
    <tbody>
        <tr>
            <td colspan="12" style="border: 1px solid: #000 !important; text-align: center">
                <h1 style="margin:0;font-weight:bold">{{$evento->name}}</h1>
            </td>
        </tr>
        <tr>
            <td colspan="12" style="border: 1px solid: #000 !important; text-align: center">
                <h2 style="margin:0;font-weight:bold">FICHA DE ESCALAÇÃO GERAL</h2>
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
                <td colspan="9" style="background: #000; COLOR: #FFF; TEXT-ALIGN: CENTER">
                    ATLETA
                </td>
                <td colspan="2" style="background: #000; COLOR: #FFF; TEXT-ALIGN: CENTER">
                    TABULEIRO
                </td>
            </tr>
            @php($i = 1)
            @foreach($inscricoes as $inscricao)

                <tr>
                    <td colspan="1" style="TEXT-ALIGN: CENTER">
                        {{$i++}}
                    </td>
                    <td colspan="9" style="TEXT-ALIGN: CENTER">
                        {{$inscricao->enxadrista->name}}
                    </td>
                    <td colspan="2" style="TEXT-ALIGN: CENTER">

                    </td>
                </tr>
            @endforeach
        @endforeach

        <tr>
            <td colspan="12">
                <hr style="margin-top: 3rem;">
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <p style="text-align: center">ASSINATURA DO PROFESSOR</p>
            </td>
        </tr>
    </tbody>
</table>
