				@php
					$team_awards = $evento->event_team_awards()->orderBy('name', 'ASC')->get();
					$team_award_add_url = url('/evento/'.$evento->id.'/premiacao_time/add');
					$team_award_edit_url_prefix = url('/evento/'.$evento->id.'/premiacao_time/edit');
					$team_award_remove_url_prefix = url('/evento/'.$evento->id.'/premiacao_time/remove');
					$can_edit_team_awards = (
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					);
					$context_label = 'evento';
				@endphp
				@include('partials.team_award_list_tab')
			
