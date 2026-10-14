<?php

return [
    'not_chesscom_software' => 'Este torneio não está configurado para integração com o Chess.com.',
    'slug_not_configured' => 'Informe o slug do torneio do Chess.com nas configurações do torneio antes de executar esta ação.',
    'tournament_not_found' => 'Torneio não encontrado no Chess.com. Verifique o slug informado.',
    'tournament_not_finished' => 'O torneio no Chess.com ainda não foi finalizado. Aguarde a conclusão para conferir as inscrições.',
    'check_players_in' => [
        'success' => 'Conferência de inscrições no Chess.com concluída com sucesso.',
        'success_with_not_found' => 'Conferência concluída. Os seguintes usuários do Chess.com não foram encontrados como inscritos no torneio: :users',
    ],
    'get_results' => [
        'success' => 'Resultados do torneio Chess.com importados com sucesso.',
    ],
    'admin_details' => [
        'suffix' => 'Detalhes (administrador): HTTP :code | Slug: :slug | URL: :url | Resposta: :body',
    ],
    'last_error' => [
        'page_title' => 'Último erro da integração Chess.com',
        'view_button' => 'Ver último erro da integração Chess.com',
        'none_saved' => 'Não há registro de erro recente da integração Chess.com para este torneio.',
        'view_details' => 'Consulte o registro completo em: :url',
        'flash_with_link' => ':message Detalhes completos: :url',
        'action_check_players_in' => 'Conferir inscrições',
        'action_get_results' => 'Importar resultados',
        'field_action' => 'Ação',
        'field_occurred_at' => 'Data/hora',
        'field_user_id' => 'Usuário (ID)',
        'field_message' => 'Mensagem',
        'field_http_status' => 'HTTP',
        'field_slug' => 'Slug',
        'field_request_url' => 'URL da requisição',
        'field_response_body' => 'Corpo da resposta',
    ],
];
