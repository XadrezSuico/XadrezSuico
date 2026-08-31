@php
    $statusClasses = [
        'Integrado' => 'status-integrado',
        'Erro' => 'status-erro',
        'Pendente' => 'status-pendente',
        'Sem ID' => 'status-sem-id',
    ];
@endphp

<div class="overflow-x-auto">
    <table id="tabela-integracao" class="display w-full text-sm" style="width: 100%">
        <thead>
            <tr>
                <th class="display-none">Ordem</th>
                <th>ID Enxadrista</th>
                <th>Nome do Enxadrista</th>
                <th>ID {{ $dados['entidade_label'] }}</th>
                <th>Nome {{ $dados['entidade_label'] }}</th>
                <th>Última Atualização</th>
                <th>Status</th>
                <th>Parecer de Nome</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dados['linhas'] as $linha)
                @php
                    $statusOrdem = ['Erro' => 0, 'Pendente' => 1, 'Integrado' => 2, 'Sem ID' => 3];
                    $ordem = $statusOrdem[$linha['status']] ?? 9;
                @endphp
                <tr>
                    <td class="display-none">{{ $ordem }}</td>
                    <td>
                        <a href="{{ url('/enxadrista/edit/' . $linha['enxadrista_id']) }}" class="text-brand hover:underline">
                            {{ $linha['enxadrista_id'] }}
                        </a>
                    </td>
                    <td>{{ $linha['nome'] }}</td>
                    <td>{{ $linha['entity_id'] }}</td>
                    <td>{{ $linha['entity_name'] }}</td>
                    <td data-order="{{ $linha['last_update'] ?? '' }}">{{ $linha['last_update_formatted'] }}</td>
                    <td>
                        <span class="status-badge {{ $statusClasses[$linha['status']] ?? 'status-sem-id' }}">
                            {{ $linha['status'] }}
                        </span>
                    </td>
                    <td>
                        @include('relatorios._partials.parecer_badge', ['resultado' => $linha['comparacao']])
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
