				@php
					$timeline_items = $evento->timeline_items()->orderBy('order', 'ASC')->get();
					$timeline_max_order = $timeline_items->max('order');
					$timeline_next_order = ($timeline_max_order !== null ? (int) $timeline_max_order : 0) + 1;
					$timeline_add_url = url('/evento/'.$evento->id.'/timeline/add');
					$timeline_edit_url_prefix = url('/evento/'.$evento->id.'/timeline/edit');
					$timeline_remove_url_prefix = url('/evento/'.$evento->id.'/timeline/remove');
					$can_edit_timeline = (
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
					);
				@endphp
				@include('partials.event_timeline_tab')
			
