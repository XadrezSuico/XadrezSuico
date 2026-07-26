<?php

namespace App\Http\Controllers;

use App\Evento;
use App\EventTimelineItem;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventTimelineItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function add($evento_id, Request $request)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect('/');
        }

        $title = trim((string) $request->input('title'));
        if ($title === '') {
            return redirect('/evento/dashboard/' . $evento_id . '?tab=timeline')
                ->with('status', 'Informe o título do item.');
        }

        $datetime = $this->parseDatetime($request->input('datetime'));
        if (!$datetime) {
            return redirect('/evento/dashboard/' . $evento_id . '?tab=timeline')
                ->with('status', 'Data e hora inválidas. Use o formato dd/mm/aaaa hh:mm.');
        }

        $maxOrder = $evento->timeline_items()->max('order');
        $order = $request->input('order');
        if ($order === null || $order === '') {
            $order = ($maxOrder !== null ? (int) $maxOrder : 0) + 1;
        } else {
            $order = (int) $order;
        }

        $item = new EventTimelineItem;
        $item->event_id = $evento->id;
        $item->title = $title;
        $item->datetime = $datetime->format('Y-m-d H:i:s');
        $item->is_expected = $request->has('is_expected');
        $item->order = $order;
        $item->save();

        return redirect('/evento/dashboard/' . $evento_id . '?tab=timeline')
            ->with('status', 'Item de timeline criado.');
    }

    public function edit($evento_id, $item_id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect('/');
        }

        $item = $this->findItem($evento_id, $item_id);
        if (!$item) {
            return redirect('/evento/dashboard/' . $evento_id . '?tab=timeline')
                ->with('status', 'Item não encontrado.');
        }

        return view('evento.timeline.edit', compact('evento', 'item'));
    }

    public function edit_post($evento_id, $item_id, Request $request)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect('/');
        }

        $item = $this->findItem($evento_id, $item_id);
        if (!$item) {
            return redirect('/evento/dashboard/' . $evento_id . '?tab=timeline')
                ->with('status', 'Item não encontrado.');
        }

        $title = trim((string) $request->input('title'));
        if ($title === '') {
            return redirect('/evento/' . $evento_id . '/timeline/edit/' . $item_id)
                ->with('status', 'Informe o título do item.');
        }

        $datetime = $this->parseDatetime($request->input('datetime'));
        if (!$datetime) {
            return redirect('/evento/' . $evento_id . '/timeline/edit/' . $item_id)
                ->with('status', 'Data e hora inválidas. Use o formato dd/mm/aaaa hh:mm.');
        }

        $item->title = $title;
        $item->datetime = $datetime->format('Y-m-d H:i:s');
        $item->is_expected = $request->has('is_expected');
        $item->order = (int) $request->input('order', $item->order);
        $item->save();

        return redirect('/evento/dashboard/' . $evento_id . '?tab=timeline')
            ->with('status', 'Item de timeline atualizado.');
    }

    public function remove($evento_id, $item_id)
    {
        $evento = Evento::find($evento_id);
        $user = Auth::user();
        if (!$evento || !$this->canEditEvento($user, $evento)) {
            return redirect('/');
        }

        $item = $this->findItem($evento_id, $item_id);
        if ($item) {
            $item->delete();
        }

        return redirect('/evento/dashboard/' . $evento_id . '?tab=timeline')
            ->with('status', 'Item de timeline removido.');
    }

    private function parseDatetime($value)
    {
        if ($value === null || trim((string) $value) === '') {
            return false;
        }
        $datetime = DateTime::createFromFormat('d/m/Y H:i', trim((string) $value));
        if (!$datetime) {
            return false;
        }
        return $datetime;
    }

    private function findItem($evento_id, $item_id)
    {
        return EventTimelineItem::where([
            ['id', '=', $item_id],
            ['event_id', '=', $evento_id],
        ])->first();
    }

    private function canEditEvento($user, Evento $evento)
    {
        return $user->hasPermissionGlobal()
            || $user->hasPermissionEventByPerfil($evento->id, [4])
            || $user->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7]);
    }
}
