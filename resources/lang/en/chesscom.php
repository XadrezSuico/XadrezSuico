<?php

return [
    'not_chesscom_software' => 'This tournament is not configured for Chess.com integration.',
    'slug_not_configured' => 'Enter the Chess.com tournament slug in the tournament settings before running this action.',
    'tournament_not_found' => 'Tournament not found on Chess.com. Check the slug provided.',
    'tournament_not_finished' => 'The Chess.com tournament has not finished yet. Wait until it is completed before checking registrations.',
    'check_players_in' => [
        'success' => 'Chess.com registration check completed successfully.',
        'success_with_not_found' => 'Check completed. The following Chess.com users were not found as registered in the tournament: :users',
    ],
    'get_results' => [
        'success' => 'Chess.com tournament results imported successfully.',
    ],
    'admin_details' => [
        'suffix' => 'Details (administrator): HTTP :code | Slug: :slug | URL: :url | Response: :body',
    ],
    'last_error' => [
        'page_title' => 'Latest Chess.com integration error',
        'view_button' => 'View latest Chess.com integration error',
        'none_saved' => 'There is no recent Chess.com integration error recorded for this tournament.',
        'view_details' => 'See the full log at: :url',
        'flash_with_link' => ':message Full details: :url',
        'action_check_players_in' => 'Check registrations',
        'action_get_results' => 'Import results',
        'field_action' => 'Action',
        'field_occurred_at' => 'Date/time',
        'field_user_id' => 'User (ID)',
        'field_message' => 'Message',
        'field_http_status' => 'HTTP',
        'field_slug' => 'Slug',
        'field_request_url' => 'Request URL',
        'field_response_body' => 'Response body',
    ],
];
