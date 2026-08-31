<?php

namespace App\Services;

use App\Enxadrista;
use App\Evento;
use App\Helper\NameComparisonHelper;
use App\Inscricao;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class RelatorioService
{
    const MODOS_COMPARACAO = ['cbx', 'fide', 'cbx-fide'];

    const ENTIDADES = ['cbx', 'fide'];

    public function buildComparacaoLinhasGlobal(string $modo): array
    {
        $incluirCbx = in_array($modo, ['cbx', 'cbx-fide'], true);
        $incluirFide = in_array($modo, ['fide', 'cbx-fide'], true);

        $linhas = [];

        foreach (Enxadrista::orderBy('name')->get() as $enxadrista) {
            $cbx = $incluirCbx ? NameComparisonHelper::compareEntity($enxadrista, 'cbx') : null;
            $fide = $incluirFide ? NameComparisonHelper::compareEntity($enxadrista, 'fide') : null;

            $niveis = [];
            if ($cbx !== null) {
                $niveis[] = $cbx['nivel'];
            }
            if ($fide !== null) {
                $niveis[] = $fide['nivel'];
            }

            $linhas[] = [
                'enxadrista_id' => $enxadrista->id,
                'nome' => $enxadrista->getNomePrivado(),
                'cbx' => $cbx,
                'fide' => $fide,
                'nivel_ordenacao' => count($niveis) > 0 ? min($niveis) : 999,
            ];
        }

        return $this->sortComparacaoLinhas($linhas);
    }

    public function buildComparacaoLinhasEvento(Evento $evento): array
    {
        $linhas = [];

        if (!$evento->calcula_cbx && !$evento->calcula_fide) {
            return $linhas;
        }

        $inscricoes = Inscricao::with('enxadrista')
            ->whereHas('torneio', function ($query) use ($evento) {
                $query->where('evento_id', $evento->id);
            })
            ->get();

        foreach ($inscricoes as $inscricao) {
            if (!$inscricao->enxadrista) {
                continue;
            }

            $cbx = $evento->calcula_cbx
                ? NameComparisonHelper::compareEntity($inscricao->enxadrista, 'cbx')
                : null;
            $fide = $evento->calcula_fide
                ? NameComparisonHelper::compareEntity($inscricao->enxadrista, 'fide')
                : null;

            $niveis = [];
            if ($cbx !== null) {
                $niveis[] = $cbx['nivel'];
            }
            if ($fide !== null) {
                $niveis[] = $fide['nivel'];
            }

            $linhas[] = [
                'inscricao_id' => $inscricao->id,
                'enxadrista_id' => $inscricao->enxadrista->id,
                'nome' => $inscricao->enxadrista->getNomePrivado(),
                'cbx' => $cbx,
                'fide' => $fide,
                'nivel_ordenacao' => count($niveis) > 0 ? min($niveis) : 999,
            ];
        }

        return $this->sortComparacaoLinhas($linhas);
    }

    public function buildIntegracaoResumo(string $entidade): array
    {
        $inicioMes = Carbon::now()->startOfMonth();

        $comIdQuery = $this->integracaoComIdQuery($entidade);
        $kpis = [
            'com_id' => (clone $comIdQuery)->count(),
            'atualizacao_mes' => (clone $comIdQuery)
                ->where("{$entidade}_last_update", '>=', $inicioMes->format('Y-m-d H:i:s'))
                ->count(),
            'erro' => $this->integracaoErroQuery($entidade)->count(),
            'pendente' => $this->integracaoPendenteQuery($entidade)->count(),
        ];

        $linhas = [];
        foreach ((clone $comIdQuery)->orderBy('name')->get() as $enxadrista) {
            $linhas[] = $this->buildIntegracaoLinha($enxadrista, $entidade, $inicioMes);
        }

        return [
            'entidade' => $entidade,
            'entidade_label' => strtoupper($entidade),
            'kpis' => $kpis,
            'linhas' => $linhas,
        ];
    }

    public function resolveIntegracaoStatus(Enxadrista $enxadrista, string $entidade, Carbon $inicioMes): string
    {
        if (!$this->hasValidEntityId($enxadrista, $entidade)) {
            return 'Sem ID';
        }

        if ($this->isIntegracaoErro($enxadrista, $entidade)) {
            return 'Erro';
        }

        if ($this->isIntegracaoPendente($enxadrista, $entidade, $inicioMes)) {
            return 'Pendente';
        }

        if ($this->isIntegrado($enxadrista, $entidade)) {
            return 'Integrado';
        }

        return 'Pendente';
    }

    private function buildIntegracaoLinha(Enxadrista $enxadrista, string $entidade, Carbon $inicioMes): array
    {
        $idField = "{$entidade}_id";
        $nameField = "{$entidade}_name";
        $updateField = "{$entidade}_last_update";
        $status = $this->resolveIntegracaoStatus($enxadrista, $entidade, $inicioMes);
        $comparacao = NameComparisonHelper::compareEntity($enxadrista, $entidade);

        return [
            'enxadrista_id' => $enxadrista->id,
            'nome' => $enxadrista->getNomePrivado(),
            'entity_id' => $enxadrista->{$idField},
            'entity_name' => trim((string) $enxadrista->{$nameField}) !== '' ? $enxadrista->{$nameField} : '-',
            'last_update' => $enxadrista->{$updateField},
            'last_update_formatted' => $enxadrista->{$updateField}
                ? Carbon::parse($enxadrista->{$updateField})->format('d/m/Y H:i')
                : '-',
            'status' => $status,
            'comparacao' => $comparacao,
        ];
    }

    private function sortComparacaoLinhas(array $linhas): array
    {
        usort($linhas, function ($a, $b) {
            if ($a['nivel_ordenacao'] === $b['nivel_ordenacao']) {
                return strcasecmp($a['nome'], $b['nome']);
            }

            return $a['nivel_ordenacao'] <=> $b['nivel_ordenacao'];
        });

        return $linhas;
    }

    private function baseIntegracaoQuery(string $entidade): Builder
    {
        $query = Enxadrista::query();

        if ($entidade === 'cbx') {
            $query->whereDoesntHave('configs', function ($q) {
                $q->where('key', 'united_to');
            });
        }

        return $query;
    }

    private function integracaoComIdQuery(string $entidade): Builder
    {
        return $this->baseIntegracaoQuery($entidade)
            ->whereNotNull("{$entidade}_id")
            ->where("{$entidade}_id", '>', 0);
    }

    private function integracaoErroQuery(string $entidade): Builder
    {
        return $this->integracaoComIdQuery($entidade)
            ->where("encontrado_{$entidade}", false)
            ->whereNotNull("{$entidade}_last_update");
    }

    private function integracaoPendenteQuery(string $entidade): Builder
    {
        $inicioMes = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');

        return $this->integracaoComIdQuery($entidade)
            ->where(function ($query) use ($entidade, $inicioMes) {
                $query->whereNull("{$entidade}_last_update")
                    ->orWhere("{$entidade}_last_update", '<', $inicioMes);
            });
    }

    private function hasValidEntityId(Enxadrista $enxadrista, string $entidade): bool
    {
        $id = $enxadrista->{"{$entidade}_id"};

        return $id !== null && intval($id) > 0;
    }

    private function isIntegracaoErro(Enxadrista $enxadrista, string $entidade): bool
    {
        return $this->hasValidEntityId($enxadrista, $entidade)
            && !$enxadrista->{"encontrado_{$entidade}"}
            && $enxadrista->{"{$entidade}_last_update"} !== null;
    }

    private function isIntegracaoPendente(Enxadrista $enxadrista, string $entidade, Carbon $inicioMes): bool
    {
        if (!$this->hasValidEntityId($enxadrista, $entidade)) {
            return false;
        }

        $lastUpdate = $enxadrista->{"{$entidade}_last_update"};

        return $lastUpdate === null || Carbon::parse($lastUpdate)->lt($inicioMes);
    }

    private function isIntegrado(Enxadrista $enxadrista, string $entidade): bool
    {
        return $this->hasValidEntityId($enxadrista, $entidade)
            && $enxadrista->{"encontrado_{$entidade}"}
            && trim((string) $enxadrista->{"{$entidade}_name"}) !== '';
    }
}
