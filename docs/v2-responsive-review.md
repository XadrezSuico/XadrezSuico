# Revisão de responsividade do layout v2

## Escopo

Auditoria estática dos templates v2, componentes compartilhados, home/login e abas legadas utilizadas pelo dashboard v2. Não foram alteradas regras de negócio.

## Correções

- Contêiner principal com `min-width: 0`, evitando que tabelas e abas aumentem a largura da página.
- Grid legado completo (xs/sm/md/lg e offset utilizado), com breakpoints Bootstrap de 768, 992 e 1200 px, sem carregar Bootstrap global.
- Rolagem horizontal própria para tabelas, incluindo inserções dinâmicas; recálculo do DataTables ao redimensionar, abrir detalhes, abas e modais.
- Nomes longos, rótulos de botões, formulários e mídia respeitam a largura disponível.
- Modais com margens em telas pequenas e rolagem vertical em telas baixas.
- Menu lateral acompanha o breakpoint de 1024 px, fecha com Escape, restaura o foco e não deixa itens ocultos receberem foco.
- Zoom do navegador liberado; login e campos móveis respeitam telas estreitas.
- CSS e JavaScript independentes do build Tailwind, carregados com versão por filemtime.

## Validação executada

Chrome headless local: 45 combinações de cinco cenários (conteúdo nativo, formulário, relatório DataTables, modal e login) com nove janelas: 320×844, 375×844, 640×900, 768×1024, 992×900, 1024×768, 1280×900, 1920×1080 e 844×390.

O teste usa o HTML estrutural dos layouts reais e assets do repositório, substituindo expressões Blade por dados sintéticos. Verifica overflow da página, larguras das colunas e modais, erros JavaScript, abertura/fechamento e resize do menu, além de tabelas adicionadas dinamicamente. As capturas de 375 px foram inspecionadas visualmente.

Limite: não houve navegação autenticada de cada tela com dados reais, nem execução do PHP/Blade local. As páginas com combinações específicas de permissões, conteúdo externo e widgets devem ser conferidas também em homologação. A tentativa de preparar uma cópia de testes no servidor foi rejeitada pela revisão automática; os testes de navegador foram mantidos locais.

Para repetir: iniciar Chrome isolado com `--headless --remote-debugging-port=9223 --user-data-dir=/tmp/xadrez-v2-review` e executar `python3 tests/browser/v2_responsiveness.py` (requer Python e websockets). Resultados e capturas ficam em `/tmp/xadrez-v2-responsive-results`.

## Páginas com layout v2

37 pontos de entrada; 97 templates em diretórios v2 e 17 arquivos das abas legadas inspecionados estaticamente.

- `auth/login.blade.php`
- `evento/v2/classificator/category/edit.blade.php`
- `evento/v2/classificator/category/new.blade.php`
- `evento/v2/classificator/edit.blade.php`
- `evento/v2/classificator/new.blade.php`
- `evento/v2/classificator/rule/edit.blade.php`
- `evento/v2/classificator/rule/new.blade.php`
- `evento/v2/dashboard.blade.php`
- `evento/v2/import/ingadigital/resultados_send.blade.php`
- `evento/v2/inscricao/gerenciar/confirmar.blade.php`
- `evento/v2/inscricao/gerenciar/inscricao.blade.php`
- `evento/v2/inscricao/gerenciar/naoha.blade.php`
- `evento/v2/inscricoes.blade.php`
- `evento/v2/relatorios/anuidade_cbx.blade.php`
- `evento/v2/relatorios/comparacao_cadastros.blade.php`
- `evento/v2/relatorios/participacao_categorias.blade.php`
- `evento/v2/relatorios/premiados.blade.php`
- `evento/v2/retornos.blade.php`
- `evento/v2/team_award/edit.blade.php`
- `evento/v2/times/classificar.blade.php`
- `evento/v2/torneio/categoria/transfer.blade.php`
- `evento/v2/torneio/edit.blade.php`
- `evento/v2/torneio/gerenciamento/3_chave_semifinal/index.blade.php`
- `evento/v2/torneio/importacao/emparceiramentos.blade.php`
- `evento/v2/torneio/importacao/emparceiramentos_retorno.blade.php`
- `evento/v2/torneio/inscricao/edit.blade.php`
- `evento/v2/torneio/inscricao/index.blade.php`
- `evento/v2/torneio/migrate_to_new_event.blade.php`
- `evento/v2/torneio/new.blade.php`
- `evento/v2/torneio/resultados.blade.php`
- `evento/v2/torneio/resultados_send.blade.php`
- `evento/v2/torneio/resultadosretorno.blade.php`
- `evento/v2/torneio/union.blade.php`
- `home.blade.php`
- `relatorios/comparacao_cadastros.blade.php`
- `relatorios/index.blade.php`
- `relatorios/resumo_integracao.blade.php`

## Inventário dos templates v2

- `components/v2/action-card.blade.php`
- `components/v2/btn.blade.php`
- `components/v2/event-inscricoes-summary.blade.php`
- `components/v2/event-table-actions.blade.php`
- `components/v2/kpi-strip.blade.php`
- `components/v2/panel.blade.php`
- `components/v2/stat-badge.blade.php`
- `components/v2/stat-card.blade.php`
- `components/v2/switch.blade.php`
- `components/v2/tabs.blade.php`
- `components/v2/torneio-inscricoes-summary.blade.php`
- `evento/publico/v2/classificacao.blade.php`
- `evento/publico/v2/list.blade.php`
- `evento/v2/_content/classificator/category/edit.blade.php`
- `evento/v2/_content/classificator/category/new.blade.php`
- `evento/v2/_content/classificator/edit.blade.php`
- `evento/v2/_content/classificator/new.blade.php`
- `evento/v2/_content/classificator/rule/edit.blade.php`
- `evento/v2/_content/classificator/rule/new.blade.php`
- `evento/v2/_content/import/ingadigital/resultados_send.blade.php`
- `evento/v2/_content/inscricao/gerenciar/confirmar.blade.php`
- `evento/v2/_content/inscricao/gerenciar/inscricao.blade.php`
- `evento/v2/_content/inscricao/gerenciar/naoha.blade.php`
- `evento/v2/_content/inscricoes.blade.php`
- `evento/v2/_content/relatorios/anuidade_cbx.blade.php`
- `evento/v2/_content/relatorios/comparacao_cadastros.blade.php`
- `evento/v2/_content/relatorios/participacao_categorias.blade.php`
- `evento/v2/_content/relatorios/premiados.blade.php`
- `evento/v2/_content/retornos.blade.php`
- `evento/v2/_content/team_award/edit.blade.php`
- `evento/v2/_content/times/classificar.blade.php`
- `evento/v2/_content/torneio/categoria/transfer.blade.php`
- `evento/v2/_content/torneio/edit.blade.php`
- `evento/v2/_content/torneio/gerenciamento/3_chave_semifinal/index.blade.php`
- `evento/v2/_content/torneio/importacao/emparceiramentos.blade.php`
- `evento/v2/_content/torneio/importacao/emparceiramentos_retorno.blade.php`
- `evento/v2/_content/torneio/inscricao/edit.blade.php`
- `evento/v2/_content/torneio/inscricao/index.blade.php`
- `evento/v2/_content/torneio/migrate_to_new_event.blade.php`
- `evento/v2/_content/torneio/new.blade.php`
- `evento/v2/_content/torneio/resultados.blade.php`
- `evento/v2/_content/torneio/resultados_send.blade.php`
- `evento/v2/_content/torneio/resultadosretorno.blade.php`
- `evento/v2/_content/torneio/union.blade.php`
- `evento/v2/_partials/dashboard_alerts.blade.php`
- `evento/v2/_scripts/dashboard.blade.php`
- `evento/v2/_tabs/funcoes.blade.php`
- `evento/v2/_tabs/resume.blade.php`
- `evento/v2/classificator/category/edit.blade.php`
- `evento/v2/classificator/category/new.blade.php`
- `evento/v2/classificator/edit.blade.php`
- `evento/v2/classificator/new.blade.php`
- `evento/v2/classificator/rule/edit.blade.php`
- `evento/v2/classificator/rule/new.blade.php`
- `evento/v2/dashboard.blade.php`
- `evento/v2/import/ingadigital/resultados_send.blade.php`
- `evento/v2/inscricao/gerenciar/confirmar.blade.php`
- `evento/v2/inscricao/gerenciar/inscricao.blade.php`
- `evento/v2/inscricao/gerenciar/naoha.blade.php`
- `evento/v2/inscricoes.blade.php`
- `evento/v2/relatorios/anuidade_cbx.blade.php`
- `evento/v2/relatorios/comparacao_cadastros.blade.php`
- `evento/v2/relatorios/participacao_categorias.blade.php`
- `evento/v2/relatorios/premiados.blade.php`
- `evento/v2/retornos.blade.php`
- `evento/v2/team_award/edit.blade.php`
- `evento/v2/times/classificar.blade.php`
- `evento/v2/torneio/categoria/transfer.blade.php`
- `evento/v2/torneio/edit.blade.php`
- `evento/v2/torneio/gerenciamento/3_chave_semifinal/index.blade.php`
- `evento/v2/torneio/importacao/emparceiramentos.blade.php`
- `evento/v2/torneio/importacao/emparceiramentos_retorno.blade.php`
- `evento/v2/torneio/inscricao/edit.blade.php`
- `evento/v2/torneio/inscricao/index.blade.php`
- `evento/v2/torneio/migrate_to_new_event.blade.php`
- `evento/v2/torneio/new.blade.php`
- `evento/v2/torneio/resultados.blade.php`
- `evento/v2/torneio/resultados_send.blade.php`
- `evento/v2/torneio/resultadosretorno.blade.php`
- `evento/v2/torneio/union.blade.php`
- `layouts/v2/app.blade.php`
- `layouts/v2/event-page.blade.php`
- `layouts/v2/event.blade.php`
- `layouts/v2/guest.blade.php`
- `layouts/v2/partials/alerts.blade.php`
- `layouts/v2/partials/event-header.blade.php`
- `layouts/v2/partials/event-legacy-plugins-scripts.blade.php`
- `layouts/v2/partials/event-legacy-plugins-styles.blade.php`
- `layouts/v2/partials/event-legacy-scoped-styles.blade.php`
- `layouts/v2/partials/event-shell-protect-styles.blade.php`
- `layouts/v2/partials/event-tabs.blade.php`
- `layouts/v2/partials/event-v2-native-styles.blade.php`
- `layouts/v2/partials/head.blade.php`
- `layouts/v2/partials/icon.blade.php`
- `layouts/v2/partials/scripts.blade.php`
- `layouts/v2/partials/sidebar.blade.php`
- `layouts/v2/partials/topbar.blade.php`
