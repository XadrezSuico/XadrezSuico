<?php

namespace App\Http\Controllers;

use App\Services\RelatorioService;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{
    /** @var RelatorioService */
    private $relatorioService;

    public function __construct(RelatorioService $relatorioService)
    {
        $this->middleware('auth');
        $this->relatorioService = $relatorioService;
    }

    public function index()
    {
        if ($redirect = $this->authorizeRelatorios()) {
            return $redirect;
        }

        return view('relatorios.index');
    }

    public function comparacaoCadastros($modo)
    {
        if ($redirect = $this->authorizeRelatorios()) {
            return $redirect;
        }

        if (!in_array($modo, RelatorioService::MODOS_COMPARACAO, true)) {
            return redirect()->route('relatorios.index');
        }

        $titulos = [
            'cbx' => 'Comparação de Cadastros CBX',
            'fide' => 'Comparação de Cadastros FIDE',
            'cbx-fide' => 'Comparação de Cadastros CBX e FIDE',
        ];

        $linhas = $this->relatorioService->buildComparacaoLinhasGlobal($modo);

        return view('relatorios.comparacao_cadastros', [
            'modo' => $modo,
            'titulo' => $titulos[$modo],
            'incluirCbx' => in_array($modo, ['cbx', 'cbx-fide'], true),
            'incluirFide' => in_array($modo, ['fide', 'cbx-fide'], true),
            'linhas' => $linhas,
        ]);
    }

    public function resumoIntegracao($entidade)
    {
        if ($redirect = $this->authorizeRelatorios()) {
            return $redirect;
        }

        if (!in_array($entidade, RelatorioService::ENTIDADES, true)) {
            return redirect()->route('relatorios.index');
        }

        $titulos = [
            'cbx' => 'Resumo de Integração CBX',
            'fide' => 'Resumo de Integração FIDE',
        ];

        $dados = $this->relatorioService->buildIntegracaoResumo($entidade);

        return view('relatorios.resumo_integracao', [
            'titulo' => $titulos[$entidade],
            'dados' => $dados,
        ]);
    }

    private function authorizeRelatorios()
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionGlobalbyPerfil([9])) {
            return redirect('/');
        }

        return null;
    }
}
