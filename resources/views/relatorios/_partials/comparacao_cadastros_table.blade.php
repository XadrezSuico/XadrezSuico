<div class="overflow-x-auto">
    <table id="tabela-comparacao" class="display w-full text-sm" style="width: 100%">
        <thead>
            <tr>
                <th class="display-none">Ordem</th>
                <th>ID Enxadrista</th>
                <th>Nome do Enxadrista</th>
                @if($incluirCbx)
                    <th>ID CBX</th>
                    <th>Nome CBX</th>
                    <th>Parecer CBX</th>
                @endif
                @if($incluirFide)
                    <th>ID FIDE</th>
                    <th>Nome FIDE</th>
                    <th>Parecer FIDE</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($linhas as $linha)
                <tr>
                    <td class="display-none">{{ $linha['nivel_ordenacao'] }}</td>
                    <td>
                        <a href="{{ url('/enxadrista/edit/' . $linha['enxadrista_id']) }}" class="text-brand hover:underline">
                            {{ $linha['enxadrista_id'] }}
                        </a>
                    </td>
                    <td>{{ $linha['nome'] }}</td>
                    @if($incluirCbx)
                        <td>{{ $linha['cbx'] ? $linha['cbx']['id'] : '-' }}</td>
                        <td>{{ $linha['cbx'] ? $linha['cbx']['nome'] : '-' }}</td>
                        <td>@include('relatorios._partials.parecer_badge', ['resultado' => $linha['cbx']])</td>
                    @endif
                    @if($incluirFide)
                        <td>{{ $linha['fide'] ? $linha['fide']['id'] : '-' }}</td>
                        <td>{{ $linha['fide'] ? $linha['fide']['nome'] : '-' }}</td>
                        <td>@include('relatorios._partials.parecer_badge', ['resultado' => $linha['fide']])</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
